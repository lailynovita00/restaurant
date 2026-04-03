<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Customer;
use App\Models\CompanyAddress;
use App\Models\RestaurantPhoneNumber;
use App\Models\SiteSetting;
use App\Services\StockService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Traits\OrderStatisticsTrait;
use App\Http\Controllers\Traits\AdminViewSharedDataTrait;
use App\Http\Controllers\Traits\OrderNumberGeneratorTrait;

class OrderController extends Controller
{
    use AdminViewSharedDataTrait;
    use OrderStatisticsTrait;
    use OrderNumberGeneratorTrait;

    public function __construct()
    {
        $this->shareAdminViewData();
        $this->shareOrderStatistics();
        
    }


    public function index(Request $request, $filter = null)
    {

        // Define allowed filters
        $allowedFilters = ['pending'];
        $availableYears = $this->getAvailableOrderYears();
        $selectedYear = null;
        $selectedMonth = null;
        $selectedOrderType = null;
        $availableMonths = [];
        $availableOrderTypes = [
            'instore' => 'Dine In',
            'delivery' => 'Online',
        ];
        $canManageAllOrdersReports = Auth::user()?->role === 'global_admin';

        if ($filter && !in_array($filter, $allowedFilters)) {
            return redirect()->route('admin.orders.index')->with('error', 'Invalid filter value.');
        }

        if (!$filter && $canManageAllOrdersReports) {
            $requestedYear = $request->integer('year');
            if ($requestedYear && in_array($requestedYear, $availableYears, true)) {
                $selectedYear = $requestedYear;
                $availableMonths = $this->getAvailableMonthsForYear($selectedYear);
            }

            $requestedMonth = $request->integer('month');
            if ($requestedMonth && $requestedMonth >= 1 && $requestedMonth <= 12) {
                $selectedMonth = $requestedMonth;
            }

            $requestedOrderType = $request->string('order_type')->toString();
            if ($requestedOrderType !== '' && array_key_exists($requestedOrderType, $availableOrderTypes)) {
                $selectedOrderType = $requestedOrderType;
            }
        }



        if ($request->ajax()) {
 
            $orders = Order::with(['orderItems:id,order_id,menu_name,quantity,sauce_name,sauce_name_ar,side_names,side_names_ar'])
                ->select(['id', 'order_no', 'created_at', 'total_price', 'status', 'cancellation_reason', 'order_type', 'table_number', 'payment_method', 'transfer_proof_path'])
                ->selectRaw('(SELECT COUNT(*) FROM orders AS o2 WHERE o2.id <= orders.id) AS order_sequence')
                ->orderBy('id', 'desc');


            // Apply filters based on the user's selection
            if ($filter) {
                if ($filter == 'pending') {
                    $orders = $orders->where('status', 'pending');
                }
            }

            if (!$filter && $canManageAllOrdersReports && ($selectedYear || $selectedMonth)) {
                if ($selectedYear) {
                    $orders = $orders->whereYear('created_at', $selectedYear);
                }
                if ($selectedMonth) {
                    $orders = $orders->whereMonth('created_at', $selectedMonth);
                }
            }

            if (!$filter && $canManageAllOrdersReports && $selectedOrderType) {
                $orders = $orders->where('order_type', $selectedOrderType);
            }

            return Datatables::of($orders)
                    ->addIndexColumn()
                    ->addColumn('action', function ($order) {
                        $smallBtnStyle = 'padding:2px 6px;font-size:11px;line-height:1.1;';
                        $userRole = (string) (Auth::user()?->role ?? '');

                        $viewButton = '<a href="'.route('admin.order.show', $order->id).'" class="btn btn-primary btn-sm" style="'.$smallBtnStyle.'" title="View Details"><i class="fa fa-eye"></i></a>';
                        $printSaveButton = '<a href="'.route('admin.order.receipt', $order->id).'?autoprint=1" target="_blank" class="btn btn-light btn-sm" style="'.$smallBtnStyle.'border:1px solid #ced4da;" title="Print / Save PDF"><i class="fa fa-print"></i></a>';

                        $deleteButton = '';
                        if ($userRole !== 'cashier') {
                            $deleteButton = $userRole === 'global_admin'
                                ? '<button type="button" class="btn btn-danger btn-sm" style="'.$smallBtnStyle.'" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="'.$order->id.'"><i class="fa fa-trash"></i></button>'
                                : '<button type="button" class="btn btn-danger btn-sm" style="'.$smallBtnStyle.'" disabled title="Hanya global admin"><i class="fa fa-trash"></i></button>';
                        }
                                            
                        return '<div class="d-flex flex-nowrap align-items-center" style="gap:4px;white-space:nowrap;">'
                            . $viewButton
                            . $printSaveButton
                            . $deleteButton
                            . '</div>';
                    })
                                                                ->addColumn('update_order', function ($order) {
                                                                    if ($order->status !== 'pending') {
                                                                        return '<span class="text-muted">-</span>';
                                                                    }

                                                                    return '<div class="d-flex flex-column" style="gap: 6px; min-width: 260px;">'
                                                                        . '<input type="hidden" id="order-status-select-' . $order->id . '" value="">'
                                                                        . '<div class="d-flex align-items-center" style="gap: 6px;">'
                                                                        . '<div class="dropdown">'
                                                                        . '<button class="btn btn-outline-secondary btn-sm dropdown-toggle bilingual-stack" type="button" id="order-status-dropdown-' . $order->id . '" data-bs-toggle="dropdown" aria-expanded="false">'
                                                                        . '<span class="bi-en">Select status</span>'
                                                                        . '<span class="bi-ar">اختر الحالة</span>'
                                                                        . '</button>'
                                                                        . '<ul class="dropdown-menu" aria-labelledby="order-status-dropdown-' . $order->id . '">'
                                                                        . '<li><button class="dropdown-item order-status-option bilingual-stack" type="button" data-order-id="' . $order->id . '" data-status="completed" data-label-en="Completed" data-label-ar="مكتمل"><span class="bi-en">Completed</span><span class="bi-ar">مكتمل</span></button></li>'
                                                                        . '<li><button class="dropdown-item order-status-option bilingual-stack" type="button" data-order-id="' . $order->id . '" data-status="cancelled" data-label-en="Canceled" data-label-ar="ملغي"><span class="bi-en">Canceled</span><span class="bi-ar">ملغي</span></button></li>'
                                                                        . '</ul>'
                                                                        . '</div>'
                                                                        . '<button type="button" class="btn btn-primary btn-sm bilingual-stack" onclick="updateOrderStatus(' . $order->id . ')"><span class="bi-en">Update</span><span class="bi-ar">تحديث</span></button>'
                                                                        . '</div>'
                                                                        . '<div id="order-cancellation-reason-wrap-' . $order->id . '" style="display:none;">'
                                                                        . '<label for="order-cancellation-reason-input-' . $order->id . '" class="bilingual-stack" style="margin-bottom:4px;">'
                                                                        . '<span class="bi-en">Cancellation reason (optional)</span>'
                                                                        . '<span class="bi-ar">سبب الإلغاء (اختياري)</span>'
                                                                        . '</label>'
                                                                        . '<textarea id="order-cancellation-reason-input-' . $order->id . '" class="form-control form-control-sm" rows="2" maxlength="500" placeholder="Cancellation reason (optional) | سبب الإلغاء (اختياري)"></textarea>'
                                                                        . '</div>'
                                                                        . '</div>';
                                                                })
                    ->addColumn('ordered_items', function ($order) {
                        if ($order->orderItems->isEmpty()) {
                            return '<span class="text-muted">No items</span>';
                        }

                        $menuNames = $order->orderItems
                            ->pluck('menu_name')
                            ->filter()
                            ->unique()
                            ->values();

                        $matchedMenus = Menu::select(['name', 'name_ar'])
                            ->where(function ($query) use ($menuNames) {
                                $query->whereIn('name', $menuNames)
                                    ->orWhereIn('name_ar', $menuNames);
                            })
                            ->get();

                        return $order->orderItems
                            ->map(function ($item) {
                                return $item;
                            })
                            ->map(function ($item) use ($matchedMenus) {
                                $qty = (int) $item->quantity;
                                $matchedMenu = $matchedMenus->first(function ($menu) use ($item) {
                                    return $menu->name === $item->menu_name || $menu->name_ar === $item->menu_name;
                                });

                                $englishName = e($matchedMenu?->name ?: $item->menu_name);
                                $arabicName = e($matchedMenu?->name_ar ?: $item->menu_name);

                                $sauceLine = '';
                                if (!empty($item->sauce_name)) {
                                    $sauceLine = '<div class="order-item-meta">'
                                        . 'Sauce: ' . e($item->sauce_name)
                                        . (!empty($item->sauce_name_ar)
                                            ? '<div class="order-item-meta-ar" dir="rtl" lang="ar">الصوص: ' . e($item->sauce_name_ar) . '</div>'
                                            : '')
                                        . '</div>';
                                }

                                $sidesLine = '';
                                $sideNames = is_array($item->side_names) ? array_values(array_filter($item->side_names)) : [];
                                $sideNamesAr = is_array($item->side_names_ar) ? array_values(array_filter($item->side_names_ar)) : [];
                                if (!empty($sideNames)) {
                                    $sidesLine = '<div class="order-item-meta">'
                                        . 'Sides: ' . e(implode(', ', $sideNames))
                                        . (!empty($sideNamesAr)
                                            ? '<div class="order-item-meta-ar" dir="rtl" lang="ar">الأصناف الجانبية: ' . e(implode('، ', $sideNamesAr)) . '</div>'
                                            : '')
                                        . '</div>';
                                }

                                return '<div class="order-item-stack">'
                                    . '<div class="order-item-en">' . $qty . 'x ' . $englishName . '</div>'
                                    . '<div class="order-item-ar">' . $qty . 'x ' . $arabicName . '</div>'
                                    . $sauceLine
                                    . $sidesLine
                                    . '</div>';
                            })
                            ->implode('');
                    })
                    ->editColumn('order_no', function ($order) {
                        return '#'.(int) ($order->order_sequence ?? 0);
                    })                   
                    ->editColumn('created_at', function ($order) {
                        return $order->created_at
                            ->copy()
                            ->timezone('Africa/Cairo')
                            ->format('g:i A - j M, Y');
                    })          
 
                    ->editColumn('total_price', function ($order) {
                        //Get Site Settings
                        $site_settings      =   SiteSetting::latest()->first();
                        $currency_symbol    =   $site_settings->currency_symbol ?? config('site.currency_symbol');

                        return number_format($order->total_price, 2) . ' ' . html_entity_decode($currency_symbol);

                    })
                    ->editColumn('status', function ($order) {
                        switch ($order->status) {
                            case 'pending':
                                return '<span class="badge badge-danger"><i class="fa fa-exclamation-circle"></i> ' . ucfirst($order->status) . '</span>';
                            case 'active':
                                return '<span class="badge badge-warning"><i class="fa fa-clock"></i> ' . ucfirst($order->status) . '</span>';
                            case 'completed':
                                return '<span class="badge badge-success"><i class="fa fa-check"></i> ' . ucfirst($order->status) . '</span>';
                            case 'cancelled':
                                $reasonHtml = '';
                                if (!empty($order->cancellation_reason)) {
                                    $reasonHtml = '<div class="order-cancel-reason">'
                                        . '<div class="order-cancel-reason-en">Reason: ' . e($order->cancellation_reason) . '</div>'
                                        . '<div class="order-cancel-reason-ar" dir="rtl" lang="ar">السبب: ' . e($order->cancellation_reason) . '</div>'
                                        . '</div>';
                                }

                                return '<span class="badge badge-secondary"><i class="fa fa-times-circle"></i> Cancelled</span>' . $reasonHtml;
                            default:
                                return ucfirst($order->status);
                        }
                    })
                    
                    ->editColumn('order_type', function ($order) {
                        return match ($order->order_type) {
                            'instore' => 'Dine In',
                            'delivery' => 'Online',
                            'pickup' => 'Pickup',
                            default => ucfirst((string) $order->order_type),
                        };
                    })
                    ->editColumn('payment_method', function ($order) {
                        if ($order->order_type !== 'delivery') {
                            return '-';
                        }

                        $paymentMethod = strtolower((string) $order->payment_method);
                        $paymentLabel = match ($paymentMethod) {
                            'cod' => 'CASH',
                            'instapay' => 'INSTAPAY',
                            'vodafone_cash' => 'VODAFONE CASH',
                            default => strtoupper(str_replace('_', ' ', (string) $order->payment_method)) ?: '-',
                        };

                        $isManualTransfer = in_array($paymentMethod, ['instapay', 'vodafone_cash'], true);

                        if ($isManualTransfer && !empty($order->transfer_proof_path)) {
                            $proofUrl = route('admin.order.transfer-proof', $order->id);

                            return '<div class="d-flex flex-nowrap align-items-center" style="gap:6px;white-space:nowrap;">'
                                . '<span>' . e($paymentLabel) . '</span>'
                                . '<a href="' . $proofUrl . '" target="_blank" class="btn btn-info btn-sm" style="padding:1px 6px;font-size:11px;line-height:1.1;" title="View Transfer Proof"><i class="fa fa-eye"></i></a>'
                                . '</div>';
                        }

                        return e($paymentLabel);
                    })
                    ->editColumn('table_number', function ($order) {
                        return $order->table_number ?? 'N/A';
                    })                   
                    ->rawColumns(['action','status','update_order','ordered_items','payment_method'])
                    ->make(true);
        }
          
        return view('admin.orders-index', compact('filter', 'availableYears', 'selectedYear', 'availableMonths', 'selectedMonth', 'selectedOrderType', 'availableOrderTypes', 'canManageAllOrdersReports'));
    }

    public function download(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->role === 'global_admin', 403);

        $availableYears = $this->getAvailableOrderYears();
        $selectedYear = $request->integer('year');
        $selectedYear = $selectedYear && in_array($selectedYear, $availableYears, true) ? $selectedYear : null;

        $selectedMonth = null;
        $requestedMonth = $request->integer('month');
        if ($requestedMonth && $requestedMonth >= 1 && $requestedMonth <= 12) {
            $selectedMonth = $requestedMonth;
        }

        $selectedOrderType = null;
        $requestedOrderType = $request->string('order_type')->toString();
        if (in_array($requestedOrderType, ['instore', 'delivery'], true)) {
            $selectedOrderType = $requestedOrderType;
        }

        $siteSettings = SiteSetting::latest()->first();
        $currencySymbol = html_entity_decode($siteSettings->currency_symbol ?? config('site.currency_symbol'));

        $orders = Order::with(['orderItems:id,order_id,menu_name,quantity,sauce_name,sauce_name_ar,side_names,side_names_ar'])
            ->select(['id', 'created_at', 'total_price', 'status', 'table_number'])
            ->when($selectedYear || $selectedMonth, function ($query) use ($selectedYear, $selectedMonth) {
                if ($selectedYear) {
                    $query->whereYear('created_at', $selectedYear);
                }
                if ($selectedMonth) {
                    $query->whereMonth('created_at', $selectedMonth);
                }
            })
            ->orderByDesc('id')
            ->get();

        if ($selectedOrderType) {
            $orders = $orders->where('order_type', $selectedOrderType);
        }

        $fileName = $selectedYear && $selectedMonth
            ? 'all-orders-' . $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT)
            : ($selectedYear ? 'all-orders-' . $selectedYear : ($selectedMonth ? 'all-orders-month-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) : 'all-orders-all-years'));

        if ($selectedOrderType) {
            $fileName .= '-' . $selectedOrderType;
        }

        $fileName .= '.csv';

        return response()->streamDownload(function () use ($orders, $currencySymbol) {
            $handle = fopen('php://output', 'w');

            // Help Excel on Windows detect UTF-8 and comma-separated columns correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fwrite($handle, "sep=,\r\n");

            fputcsv($handle, ['Date', 'Order Type', 'Table Number', 'Status', 'Price', 'Ordered Items']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->created_at?->copy()?->timezone('Africa/Cairo')->format('g:i A - j M, Y'),
                    match ($order->order_type) {
                        'instore' => 'Dine In',
                        'delivery' => 'Online',
                        'pickup' => 'Pickup',
                        default => ucfirst((string) $order->order_type),
                    },
                    $order->table_number ?? 'N/A',
                    ucfirst($order->status),
                    number_format((float) $order->total_price, 2) . ' ' . $currencySymbol,
                    $this->buildOrderItemsExportText($order),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function getAvailableOrderYears(): array
    {
        $currentYear = now()->year;
        $oldestOrder = Order::query()->oldest('created_at')->value('created_at');

        if (!$oldestOrder) {
            return [$currentYear];
        }

        $startYear = $oldestOrder->copy()->year;

        return range($startYear, $currentYear);
    }

    private function getAvailableMonthsForYear(?int $year): array
    {
        if (!$year) {
            return range(1, 12);
        }

        $months = Order::query()
            ->whereYear('created_at', $year)
            ->selectRaw('DISTINCT MONTH(created_at) as month')
            ->orderBy('month')
            ->pluck('month')
            ->map(fn($m) => (int)$m)
            ->toArray();

        return !empty($months) ? $months : range(1, 12);
    }

    private function buildOrderItemsExportText($order): string
    {
        if ($order->orderItems->isEmpty()) {
            return 'No items';
        }

        $menuNames = $order->orderItems
            ->pluck('menu_name')
            ->filter()
            ->unique()
            ->values();

        $matchedMenus = Menu::select(['name', 'name_ar'])
            ->where(function ($query) use ($menuNames) {
                $query->whereIn('name', $menuNames)
                    ->orWhereIn('name_ar', $menuNames);
            })
            ->get();

        return $order->orderItems
            ->map(function ($item) use ($matchedMenus) {
                $qty = (int) $item->quantity;
                $matchedMenu = $matchedMenus->first(function ($menu) use ($item) {
                    return $menu->name === $item->menu_name || $menu->name_ar === $item->menu_name;
                });

                $englishName = $matchedMenu?->name ?: $item->menu_name;

                $itemLines = [$qty . 'x ' . $englishName];

                if (!empty($item->sauce_name)) {
                    $itemLines[] = 'Sauce: ' . $item->sauce_name;
                }

                $sideNames = is_array($item->side_names) ? array_values(array_filter($item->side_names)) : [];

                if (!empty($sideNames)) {
                    $itemLines[] = 'Sides: ' . implode(', ', $sideNames);
                }

                return implode("\n", $itemLines);
            })
            ->implode("\n\n");
    }
    
    public function show($id)
    {
        $order = Order::with(['orderItems', 'createdByUser', 'updatedByUser', 'customer', 'pickupAddress', 'deliveryAddressWithTrashed'])->findOrFail($id);

        $this->localizeOrderItems($order);
        $order->order_sequence = (int) Order::query()->where('id', '<=', $order->id)->count();
        
        return view('admin.orders-show', compact('order'));
    }

    public function receipt($id)
    {
        $order = Order::with(['orderItems', 'createdByUser', 'updatedByUser'])->findOrFail($id);
        $this->localizeOrderItems($order);
        $order->order_sequence = (int) Order::query()->where('id', '<=', $order->id)->count();

        $siteSettings = SiteSetting::latest()->first();
        $currencySymbol = html_entity_decode($siteSettings?->currency_symbol ?? config('site.currency_symbol'));

        $cashierName = $this->resolveCashierName();
        $orderSequence = $order->order_sequence;

        $phoneNumbers = RestaurantPhoneNumber::query()
            ->pluck('phone_number')
            ->filter()
            ->values()
            ->all();

        $primaryAddress = CompanyAddress::query()->first();
        $fullAddress = $primaryAddress?->full_address ?: config('site.address');

        $receiptData = [
            'restaurant_name' => strtoupper(config('site.name', 'PALOMBINI CAFE')),
            'logo_url' => asset('assets/images/palombini-logo.png'),
            'currency_symbol' => $currencySymbol,
            'cashier_name' => $cashierName,
            'table_number' => $order->table_number,
            'phone_numbers' => $phoneNumbers,
            'full_address' => $fullAddress,
            'thank_you_message' => 'Thank you for your visit',
        ];

        return view('admin.orders-receipt', compact('order', 'receiptData'));
    }

    public function transferProof($id)
    {
        $order = Order::select(['id', 'order_type', 'payment_method', 'transfer_proof_path'])->findOrFail($id);

        $paymentMethod = strtolower((string) $order->payment_method);
        $isManualTransfer = in_array($paymentMethod, ['instapay', 'vodafone_cash'], true);

        abort_unless($order->order_type === 'delivery' && $isManualTransfer, 404);

        $proofPath = ltrim((string) $order->transfer_proof_path, '/');
        if ($proofPath === '' || str_contains($proofPath, '..')) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($proofPath)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($proofPath));
    }
    


    public function createOrder(Request $request)
    {
        $cart = session()->get($request->cartkey, []);
        if (empty($cart)) {
            return back()->with('error', 'Cart is empty!');

        }

        $totalPrice = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Validate request data
        $validatedData = $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:255',
        ]);


        // Generate a unique 7-digit order number
        $order_no = $this->generateOrderNumber();

        // Create a new order
        $order = Order::create([
            'customer_id' => null,
            'order_no' => $order_no,
            'order_type' => 'instore',
            'created_by_user_id' => Auth::id(),
            'updated_by_user_id' => Auth::id(),
            'total_price' => $totalPrice,
            'status' => 'completed',
            'payment_method' => $validatedData['payment_method'] ?? 'INSTORE',
            'additional_info' => $validatedData['additional_info'],
            'delivery_fee' => null,
            'delivery_distance' => null,
            'price_per_mile' => null,

        ]);

        if ($order) {
            // Create order items using the relationship
            foreach ($cart as $item) {
                $order->orderItems()->create([
                    'menu_id' => $item['id'] ?? null,
                    'menu_name' => $item['name'],  
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'sauce_name' => $item['sauce_name'] ?? null,
                    'sauce_name_ar' => $item['sauce_name_ar'] ?? null,
                    'side_names' => $item['side_names'] ?? null,
                    'side_names_ar' => $item['side_names_ar'] ?? null,
                ]);
            }

            app(StockService::class)->applyOrderStock($order);
        }

        // Clear the cart
        session()->forget($request->cartkey);

        return redirect()->route('admin.orders.index')->with('success', 'Order Created successfully.');
    }

    
    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'status' => 'required|in:completed,cancelled',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);
        $order = Order::findOrFail($id);
        $previousStatus = (string) $order->status;

        $status = (string) $request->status;
        $cancellationReason = $status === 'cancelled'
            ? trim((string) $request->input('cancellation_reason', ''))
            : null;

        $order->update([
            'status' => $status,
            'updated_by_user_id' => Auth::id(),
            'cancellation_reason' => $cancellationReason !== '' ? $cancellationReason : null,
        ]);

        if ($status === 'completed' && $previousStatus !== 'completed') {
            app(StockService::class)->applyOrderStock($order->fresh(['orderItems']));
        }

        if ($status === 'cancelled' && $previousStatus === 'completed') {
            app(StockService::class)->restoreOrderStock($order->fresh(), 'order_cancelled');
        }
    
        return back()->with('success', 'Order status updated successfully');
    }

    public function markAsCompleted($id)
    {
        $order = Order::findOrFail($id);
        $previousStatus = (string) $order->status;
        $order->update(['status' => 'completed', 'updated_by_user_id' => Auth::id()]);

        if ($previousStatus !== 'completed') {
            app(StockService::class)->applyOrderStock($order->fresh(['orderItems']));
        }

        return response()->json(['success' => true, 'message' => 'Order marked as completed successfully']);
    }

 
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        app(StockService::class)->restoreOrderStock($order, 'order_deleted');
        $order->deleteWithRelations();

        return redirect()->route('admin.orders.index')->with('success', 'Order have been deleted successfully.');
    }

    protected function localizeOrderItems(Order $order): void
    {
        $menuNames = $order->orderItems
            ->pluck('menu_name')
            ->filter()
            ->unique()
            ->values();

        $matchedMenus = Menu::select(['name', 'name_ar'])
            ->where(function ($query) use ($menuNames) {
                $query->whereIn('name', $menuNames)
                    ->orWhereIn('name_ar', $menuNames);
            })
            ->get();

        $order->orderItems->transform(function ($item) use ($matchedMenus) {
            $matchedMenu = $matchedMenus->first(function ($menu) use ($item) {
                return $menu->name === $item->menu_name || $menu->name_ar === $item->menu_name;
            });

            if ($matchedMenu) {
                $item->menu_name_en = $matchedMenu->name ?: $item->menu_name;
                $item->menu_name_ar = $matchedMenu->name_ar;
            } else {
                $item->menu_name_en = $item->menu_name;
                $item->menu_name_ar = null;
            }

            return $item;
        });
    }

    protected function resolveCashierName(): string
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return '-';
        }

        $firstName = trim((string) $currentUser->first_name);
        if ($firstName !== '') {
            return $firstName;
        }

        return (string) ($currentUser->email ?: '-');
    }
}
