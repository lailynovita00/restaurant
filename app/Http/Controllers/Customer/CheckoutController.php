<?php

namespace App\Http\Controllers\Customer;

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Address;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Models\OrderSettings;
use App\Models\CompanyAddress;
use App\Helpers\DistanceHelper;
use App\Services\StockService;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\CartTrait;
use App\Http\Controllers\Traits\OrderNumberGeneratorTrait;
use App\Http\Controllers\Traits\MainSiteViewSharedDataTrait;

class CheckoutController extends Controller
{
    use CartTrait;
    use MainSiteViewSharedDataTrait;
    use OrderNumberGeneratorTrait;

    protected $provider;
    protected $stripeSecret;
    protected $paystackSecret;
    protected $currencyCode;

    protected $distance_limit_in_miles;
    protected $price_per_mile;
    protected $companyAddress;


    public function __construct()
    {
        $this->shareMainSiteViewData();

        $this->provider      = config('payments.provider');
        $this->stripeSecret  = config('payments.stripe.secret');
        $this->paystackSecret= config('payments.paystack.secret');  

        // Get Site Settings
        $site_settings  = SiteSetting::latest()->first();
        $this->currencyCode  = strtolower($site_settings->currency_code ?? 'usd');
        $this->companyAddress = CompanyAddress::first();

        //Order Settings
        $order_settings = OrderSettings::first();
        $this->price_per_mile = (float) ($order_settings->price_per_mile ?? 0);
        $this->distance_limit_in_miles = (float) ($order_settings->distance_limit_in_miles ?? 0);
 
    }

    // Session key for wizard data
    const SESSION_KEY = 'checkout';

    public function details()
    {
        $user = Auth::user();
        return view('main-site.checkout-details', compact('user'));
    }

    public function detailsPost(Request $request)
    {
        // confirm the user confirms their details
        $request->validate(['confirm' => 'required|accepted']);

        $order_no = $this->generateOrderNumber();
        $data = session(self::SESSION_KEY, []);
        $data['customer_confirmed'] = true;
        $data['order_no'] = $order_no;

        session([self::SESSION_KEY => $data]);

        return redirect()->route('customer.checkout.fulfilment');
    }

    /** Step 2: Fulfilment choice */
    public function fulfilment()
    {
        $this->guardStep('customer_confirmed');
        $user = Auth::user();
        return view('main-site.checkout-fulfilment', compact('user'));
    }

    public function fulfilmentPost(Request $request)
    {
        $request->validate(['method' => 'required|in:pickup,delivery']);

        $fulfilmentMethod = (string) $request->input('method');

        $data = session(self::SESSION_KEY, []);
        $data['fulfilment'] = $fulfilmentMethod;
        // reset dependent choices if user changes their mind
        unset($data['pickup_location_id'], $data['delivery']);
        session([self::SESSION_KEY => $data]);

        return $fulfilmentMethod === 'pickup'
            ? redirect()->route('customer.checkout.pickup')
            : redirect()->route('customer.checkout.delivery');
    }

    /** Step 3a: Pickup */
    public function pickup()
    {
        // Ensure customer has completed the previous step
        $this->guardStep('fulfilment', 'pickup');

        // Fetch pickup locations  
        $pickupLocations = CompanyAddress::all();

        // Send them to the view
        return view('main-site.checkout-pickup', compact('pickupLocations'));
    }

    /* step 3a: Pickup POST */
    public function pickupPost(Request $request)
    {
        $request->validate(['pickup_location_id' => 'required']);

        $data = session(self::SESSION_KEY, []);

        // Remove all delivery-specific session data
        unset($data['addresses']);

        // Save pickup location
        $data['pickup_location_id'] = $request->pickup_location_id;
        // $data['order_type'] = 'pickup';

        session([self::SESSION_KEY => $data]);

        return redirect()->route('customer.checkout.review');
    }


    /** Step 3b: Delivery */
    public function delivery()
    {
        $this->guardStep('fulfilment', 'delivery');
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        return view('main-site.checkout-delivery', compact('addresses'));
    }


    public function deliveryPost(Request $request)
    {
        $user = Auth::user();

        // ---- Base validation ----
        $v = Validator::make(
            $request->all(),
            [
                'mode' => ['required', Rule::in(['saved','new'])],

                'saved_address_id' => [
                    'nullable','integer',
                    Rule::exists('addresses','id')->where(fn($q) => $q->where('user_id', $user->id)),
                ],

                // Delivery "new" fields
                'new.line1'       => ['nullable','string','max:255'],
                'new.line2'       => ['nullable','string','max:255'],
                'new.city'        => ['nullable','string','max:150'],
                'new.state'       => ['nullable','string','max:150'],
                'new.postal_code' => ['nullable','string','max:30'],
                'new.country'     => ['nullable','string','max:150'],
                'new.latitude'    => ['nullable','numeric'],
                'new.longitude'   => ['nullable','numeric'],
            ],
            [
                // Custom friendly messages
                'saved_address_id.required' => 'Please select one of your saved addresses, or choose “Add a new address”.',
                'new.line1.required'        => 'Please enter the first line of your new address.',
                'new.city.required'         => 'Please enter the city for your new address.',
                'new.postal_code.required'  => 'Please enter the postal code for your new address.',
                'new.country.required'      => 'Please select the country for your new address.',
                'new.latitude.required'     => 'Please provide the latitude for your new address.',
                'new.longitude.required'    => 'Please provide the longitude for your new address.',        
            ]
        );

        // Optional: make field labels nicer if they appear in other messages
        $v->setAttributeNames([
            'saved_address_id'   => 'saved address',
            'new.line1'          => 'address line 1',
            'new.city'           => 'city',
            'new.postal_code'    => 'postal code',
            'new.country'        => 'country',
            'new.latitude'       => 'latitude',
            'new.longitude'      => 'longitude',
        ]);

        // ---- Conditional validation ----
        $v->sometimes('saved_address_id', 'required', fn($input) => $input->mode === 'saved');
        foreach (['new.line1','new.city','new.postal_code','new.country'] as $f) {
            $v->sometimes($f, 'required', fn($input) => $input->mode === 'new');
        }

        $v->validate();

        // ---- Create or resolve delivery address ----
        $deliveryAddressId = null;

        if ($request->mode === 'saved') {
            $deliveryAddressId = (int) $request->saved_address_id;
        } else {


 
                $origin_latitude = $this->companyAddress->latitude;
                $origin_longitude = $this->companyAddress->longitude;
                $destination_latitude = (float) $request->input('new.latitude');
                $destination_longitude = (float) $request->input('new.longitude');

 
                // Distance
                $distanceData = DistanceHelper::getDistance($origin_latitude, $origin_longitude, $destination_latitude, $destination_longitude);

                if (isset($distanceData['error'])) {
                    return back()->withErrors("Unfortunately, we are unable to deliver to the provided address. Please check the address details or contact support for assistance.");
                 }

                $distance_in_miles = $distanceData['value_in_miles'];

 
 
                if ($distance_in_miles > $this->distance_limit_in_miles) {
                    $error_message = "We're sorry! We can only deliver within {$this->distance_limit_in_miles} miles. You can still place your order as a walk-in at our restaurant located at {$this->companyAddress->full_address}. We look forward to serving you!";
                    return back()->withErrors($error_message)->withInput();
                }


                // Create new address
                $delivery = $user->addresses()->create([
                    'label'       => 'delivery',
                    'street'      => trim(($request->input('new.line1') ?? '') . ($request->filled('new.line2') ? ', '.$request->input('new.line2') : '')),
                    'city'        => $request->input('new.city'),
                    'state'       => $request->input('new.state'),
                    'postal_code' => $request->input('new.postal_code'),
                    'country'     => $request->input('new.country'),
                    'latitude'    => $request->input('new.latitude'),
                    'longitude'   => $request->input('new.longitude'),
                    'is_default'  => false,
                ]);
                $deliveryAddressId = $delivery->id;
        }

        // Store only delivery ID in session ----
        $data = session(self::SESSION_KEY, []);
        $data['addresses'] = [
            'delivery_address_id' => $deliveryAddressId,
        ];
        session([self::SESSION_KEY => $data]);

        return redirect()->route('customer.checkout.review');
    }









    /** Step 4: Review */
    public function review()
    {
        $user = Auth::user();


        // Cart checks
        if (!session()->has($this->cartkey)) {
            return redirect()->route('menu')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        $cart_items = session()->get($this->cartkey, []);

        if (empty($cart_items)) {
            return redirect()->route('menu')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }
        
        
        $delivery_fee = 0;

 
        $sessionData = session(self::SESSION_KEY, []);

        if (isset($sessionData['addresses']['delivery_address_id'])) {
 
            
            $delivery_address_id = $sessionData['addresses']['delivery_address_id'] ?? null;

            if (!$delivery_address_id) {
                return redirect()->route('customer.checkout.delivery')
                    ->withErrors('Please choose a delivery address first.');
            }

            $delivery_address = $user->addresses()->find($delivery_address_id);


 
            if (!$delivery_address) {
                return redirect()->route('customer.checkout.delivery')
                    ->withErrors('Selected delivery address was not found.');
            }




            $origin_latitude = $this->companyAddress->latitude;
            $origin_longitude = $this->companyAddress->longitude;
            $destination_latitude =  $delivery_address->latitude;
            $destination_longitude = $delivery_address->longitude;



            // Distance
            $distanceData = DistanceHelper::getDistance($origin_latitude, $origin_longitude, $destination_latitude, $destination_longitude);

            if (isset($distanceData['error'])) {
                return back()->withErrors("Unfortunately, we are unable to deliver to the provided address. Please check the address details or contact support for assistance.");
            }

            $distance_in_miles = $distanceData['value_in_miles'];


            if ($distance_in_miles > $this->distance_limit_in_miles) {
                $error_message = "We're sorry! We can only deliver within {$this->distance_limit_in_miles} miles. You can still place your order as a walk-in at our restaurant located at {$this->companyAddress->full_address}. We look forward to serving you!";
                return back()->withErrors($error_message)->withInput();
            }



            // Delivery fee
            $delivery_fee = ceil($this->price_per_mile * $distance_in_miles * 100) / 100;

            //  save delivery pricing into the same checkout session
            $sessionData['delivery'] = [
                'distance_miles' => $distance_in_miles,
                'delivery_fee'   => $delivery_fee,
             ];
            session([self::SESSION_KEY => $sessionData]);

        }

        $subtotal = array_reduce($cart_items, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return view('main-site.checkout-review', compact('user', 'cart_items', 'delivery_fee', 'subtotal'));
    }









    public function proccessCheckout(Request $request)
    {
   

        $user = Auth::user();

        // 1) Ensure there is still a cart
        if (!session()->has($this->cartkey)) {
            return redirect()->route('menu')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        $cart_items = session()->get($this->cartkey, []);

        if (empty($cart_items)) {
            return redirect()->route('menu')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        // 2) Pull checkout session data
        $checkout = session(self::SESSION_KEY, []);

 
        $fulfilment       = $checkout['fulfilment']; // 'pickup' or 'delivery'
        $addresses        = $checkout['addresses']       ?? [];
        $deliverySession  = $checkout['delivery']        ?? [];
        $order_no         = $checkout['order_no']        ?? null;

        $deliveryAddressId = $addresses['delivery_address_id'] ?? null;
        $pickupLocationId =  $checkout['pickup_location_id'] ?? null;

 
        $delivery_fee    = $deliverySession['delivery_fee']    ?? 0;
        $distance_miles  = $deliverySession['distance_miles']  ?? null;
 
        // 3) Recalculate subtotal
        $subtotal = array_reduce($cart_items, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $total = $subtotal + $delivery_fee;

        $order = Order::updateOrCreate(
            ['order_no' => $order_no],
            [
                'user_id'            => $user->id,
                'order_type'         => $fulfilment,
                'created_by_user_id' => null,
                'updated_by_user_id' => null,
                'total_price'        => $total,
                'status'             => 'pending',
                'status_online_pay'  => 'unpaid',
                'session_id'         => null,
                'payment_method'     => 'STRIPE',
                'additional_info'    => $request->input('additional_info'),
                'delivery_fee'       => $delivery_fee,
                'delivery_distance'  => $distance_miles,
                'price_per_mile'     => $this->price_per_mile,
                'delivery_address_id'=> $deliveryAddressId,
                'pickup_address_id'  => $pickupLocationId,
            ]
        );

        // If the order already existed, clear old items first
        if (! $order->wasRecentlyCreated) {
            $order->orderItems()->delete();
        }

        // Re-create items from current cart state
        foreach ($cart_items as $item) {
            $qty = (int) ($item['quantity'] ?? 1);

            $order->orderItems()->create([
                'menu_id' => $item['id'] ?? null,
                'menu_name' => $item['name'],
                'quantity'  => $qty,
                'subtotal'  => $item['price'] * $qty,
                'sauce_name' => $item['sauce_name'] ?? null,
                'sauce_name_ar' => $item['sauce_name_ar'] ?? null,
                'side_names' => $item['side_names'] ?? null,
                'side_names_ar' => $item['side_names_ar'] ?? null,
            ]);
        }

        // Initialize the line_items array
        $line_items = [];

        // Loop through the cart items to populate line_items
        foreach ($cart_items as $cart_item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => $this->currencyCode,
                    'product_data' => [
                        'name' => $cart_item['name'],
                    ],
                    'unit_amount' => $cart_item['price'] * 100, // Convert price to cents
                ],
                'quantity' => $cart_item['quantity'],
            ];
        }

        // Add delivery fee in the line_items
        if (isset($delivery_fee)) {
            $line_items[] = [
                'price_data' => [
                    'currency' => $this->currencyCode,
                    'product_data' => [
                        'name' => 'Delivery Fee',
                    ],
                    'unit_amount' => $delivery_fee * 100, // Convert to cents
                ],
                'quantity' => 1,
            ];
        }



        if ($this->provider === 'stripe') {
            return $this->processStripePayment($order, $line_items, $user);
        }

        if ($this->provider === 'paystack') {
            return $this->processPaystackPayment($order, $total, $user);
        }




    }

    /** Helpers */
    private function guardStep(string $key, $value = null): void
    {
        $data = session(self::SESSION_KEY, []);
        if (!array_key_exists($key, $data)) {
            redirect()->route('customer.checkout.details')->send();
        }
        if (!is_null($value) && ($data[$key] !== $value)) {
            redirect()->route('customer.checkout.fulfilment')->send();
        }
    }






    private function processStripePayment($order, $line_items, $user)
    {
        Stripe::setApiKey($this->stripeSecret);

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'customer_email' => $user->email,
            'metadata' => [
                'order_no' => $order->order_no,
            ],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        return redirect($checkout_session->url);
    }



    private function processPaystackPayment($order, $amount, $user)
    {
         $amountSmallestUnit = (int) round($amount * 100);

        $response = Http::withToken($this->paystackSecret)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'        => $user->email,
                'amount'       => $amountSmallestUnit,
                'currency'     => strtoupper($this->currencyCode),
                'metadata'     => [
                    'order_no' => $order->order_no,
                ],
                'callback_url' => route('payment.success'),
            ]);

              
        if (! $response->successful()) {
            return back()->withErrors('Unable to contact Paystack. Please try again.');
        }

        $data = $response->json();

        if (empty($data['status']) || empty($data['data']['authorization_url'])) {
            return back()->withErrors($data['message'] ?? 'Payment initialization failed.');
        }

        // Redirect customer to Paystack checkout page
        return redirect($data['data']['authorization_url']);
    }

    public function dineInCheckout(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|min:1',
            'customer_phone' => 'nullable|string|max:30',
            'additional_info' => 'nullable|string|max:500',
        ]);

        $user = Auth::check() ? Auth::user() : null;
        $customerUserId = ($user && ($user->role ?? null) === 'customer') ? $user->id : null;

        // Ensure there is still a cart
        if (!session()->has($this->cartkey)) {
            return redirect()->route('customer.cart')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        $cart_items = session()->get($this->cartkey, []);

        if (empty($cart_items)) {
            return redirect()->route('customer.cart')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        // Recalculate subtotal
        $subtotal = array_reduce($cart_items, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Generate order number
        $order_no = $this->generateOrderNumber();

        // Create the order
        $order = Order::create([
            'order_no' => $order_no,
            'user_id' => $customerUserId,
            'order_type' => 'instore',
            'created_by_user_id' => null,
            'updated_by_user_id' => null,
            'total_price' => $subtotal,
            'status' => 'pending', // Use existing enum value available in current schema
            'status_online_pay' => 'paid', // No payment needed for dine-in
            'session_id' => session()->getId(),
            'payment_method' => 'dinein',
            'additional_info' => $request->input('additional_info'),
            'delivery_fee' => 0,
            'delivery_distance' => null,
            'price_per_mile' => null,
            'delivery_address_id' => null,
            'pickup_address_id' => null,
            'table_number' => $request->input('table_number'),
            'customer_phone' => $request->input('customer_phone'),
        ]);

        // Create order items
        foreach ($cart_items as $item) {
            $qty = (int) ($item['quantity'] ?? 1);

            $order->orderItems()->create([
                'menu_id' => $item['id'] ?? null,
                'menu_name' => $item['name'],
                'quantity' => $qty,
                'subtotal' => $item['price'] * $qty,
                'sauce_name' => $item['sauce_name'] ?? null,
                'sauce_name_ar' => $item['sauce_name_ar'] ?? null,
                'side_names' => $item['side_names'] ?? null,
                'side_names_ar' => $item['side_names_ar'] ?? null,
            ]);
        }

        // Reset cart and checkout wizard session so the site behaves like a fresh visit.
        session()->forget([$this->cartkey, self::SESSION_KEY]);

        return redirect()->route('customer.cart')->with('order_success_popup', [
            'order_no' => $order_no,
            'order_type' => 'instore',
            'table_number' => (int) $request->input('table_number'),
        ]);
    }

    public function onlineCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'delivery_address' => 'required|string|max:1000',
            'payment_method' => ['required', Rule::in(['cod', 'instapay', 'vodafone_cash'])],
            'additional_info' => 'nullable|string|max:500',
            'transfer_proof' => [
                Rule::requiredIf(fn () => in_array($request->input('payment_method'), ['instapay', 'vodafone_cash'], true)),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:5120',
            ],
        ]);

        $user = Auth::check() ? Auth::user() : null;
        $customerUserId = ($user && ($user->role ?? null) === 'customer') ? $user->id : null;

        if (!session()->has($this->cartkey)) {
            return redirect()->route('customer.cart')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        $cartItems = session()->get($this->cartkey, []);

        if (empty($cartItems)) {
            return redirect()->route('customer.cart')->withErrors('Your cart is empty. Please add items to your cart before checking out.');
        }

        $subtotal = array_reduce($cartItems, function ($carry, $item) {
            return $carry + (($item['price'] ?? 0) * ($item['quantity'] ?? 0));
        }, 0);

        $orderNo = $this->generateOrderNumber();
        $paymentMethod = $request->input('payment_method');
        $transferProofPath = null;

        if ($request->hasFile('transfer_proof')) {
            $transferProofPath = $request->file('transfer_proof')->store('transfer-proofs', 'public');
        }

        $order = Order::create([
            'order_no' => $orderNo,
            'user_id' => $customerUserId,
            'order_type' => 'delivery',
            'created_by_user_id' => null,
            'updated_by_user_id' => null,
            'total_price' => $subtotal,
            'status' => 'pending',
            'status_online_pay' => $paymentMethod === 'cod' ? 'unpaid' : 'unpaid',
            'session_id' => session()->getId(),
            'payment_method' => $paymentMethod,
            'additional_info' => $request->input('additional_info'),
            'delivery_fee' => 0,
            'delivery_distance' => null,
            'price_per_mile' => null,
            'delivery_address_id' => null,
            'pickup_address_id' => null,
            'table_number' => null,
            'customer_phone' => trim((string) $request->input('customer_phone')),
            'online_customer_name' => trim((string) $request->input('customer_name')),
            'online_customer_phone' => trim((string) $request->input('customer_phone')),
            'online_delivery_address' => trim((string) $request->input('delivery_address')),
            'transfer_proof_path' => $transferProofPath,
        ]);

        foreach ($cartItems as $item) {
            $qty = (int) ($item['quantity'] ?? 1);

            $order->orderItems()->create([
                'menu_id' => $item['id'] ?? null,
                'menu_name' => $item['name'],
                'quantity' => $qty,
                'subtotal' => ($item['price'] ?? 0) * $qty,
                'sauce_name' => $item['sauce_name'] ?? null,
                'sauce_name_ar' => $item['sauce_name_ar'] ?? null,
                'side_names' => $item['side_names'] ?? null,
                'side_names_ar' => $item['side_names_ar'] ?? null,
            ]);
        }

        session()->forget([$this->cartkey, self::SESSION_KEY]);

        return redirect()->route('customer.cart')->with('order_success_popup', [
            'order_no' => $orderNo,
            'order_type' => 'delivery',
            'customer_name' => $order->online_customer_name,
        ]);
    }

}
