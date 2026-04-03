<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Sauce;
use App\Models\Side;
use Illuminate\Support\Facades\Schema;

trait CartTrait
{
    public function addToCart(Request $request)
    {
        // Validate the request
        $request->validate([
            'cartkey' => 'required|string',
            'id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'img_src' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'sauce_id' => 'nullable|integer',
            'sauce_name' => 'nullable|string|max:255',
            'sauce_name_ar' => 'nullable|string|max:255',
            'side_ids' => 'nullable|array',
            'side_ids.*' => 'integer',
            'side_names' => 'nullable|array',
            'side_names.*' => 'string|max:255',
            'side_names_ar' => 'nullable|array',
            'side_names_ar.*' => 'nullable|string|max:255',
        ]);

        $menu = Menu::with('category')->find($request->id);
        $selectedSauceId = $request->input('sauce_id');
        $selectedSideIds = collect($request->input('side_ids', []))
            ->filter(fn ($id) => !is_null($id) && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($menu && $menu->category && $menu->category->requires_sauce && empty($selectedSauceId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a sauce for this appetizer.',
            ], 422);
        }

        if (!empty($selectedSauceId)) {
            $sauceExists = Sauce::where('id', $selectedSauceId)
                ->where('is_active', true)
                ->exists();

            if (! $sauceExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected sauce is unavailable.',
                ], 422);
            }
        }

        $selectedSideNames = [];
        $selectedSideNamesAr = [];

        if (
            $menu
            && $menu->category
            && $menu->category->requires_side
            && Schema::hasTable('sides')
            && Schema::hasTable('category_side')
        ) {
            $menu->category->load('sides');

            if ($selectedSideIds->count() !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select exactly 2 sides for this main dish.',
                ], 422);
            }

            $allowedSideIds = $menu->category->sides
                ->where('is_active', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            $hasInvalidSide = $selectedSideIds->contains(function ($id) use ($allowedSideIds) {
                return ! $allowedSideIds->contains($id);
            });

            if ($hasInvalidSide) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more selected sides are unavailable.',
                ], 422);
            }

            $selectedSides = Side::whereIn('id', $selectedSideIds->all())
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'name_ar']);

            if ($selectedSides->count() !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select exactly 2 available sides.',
                ], 422);
            }

            $selectedSideIds = $selectedSides->pluck('id')->map(fn ($id) => (int) $id)->values();
            $selectedSideNames = $selectedSides->pluck('name')->values()->all();
            $selectedSideNamesAr = $selectedSides->pluck('name_ar')->values()->all();
        }

        $cart = session()->get($request->cartkey, []);
        $quantity = (int) $request->input('quantity', 1);
        $lineKey = (string) $request->id;

        if (!empty($selectedSauceId)) {
            $lineKey .= ':s' . ((string) $selectedSauceId);
        }

        if ($selectedSideIds->isNotEmpty()) {
            $lineKey .= ':sd' . $selectedSideIds->implode('-');
        }
    
        if (isset($cart[$lineKey])) {
            // Increase by requested quantity if item already exists in cart
            $cart[$lineKey]['quantity'] += $quantity;
        } else {
            // Otherwise, add the item to the cart
            $cart[$lineKey] = [
                'line_key' => $lineKey,
                'id' => $request->id,
                'name' => $request->name,
                'price' => $request->price,
                'img_src' => $request->img_src ?? '',
                'quantity' => $quantity,
                'sauce_id' => $selectedSauceId ? (int) $selectedSauceId : null,
                'sauce_name' => $request->input('sauce_name'),
                'sauce_name_ar' => $request->input('sauce_name_ar'),
                'side_ids' => $selectedSideIds->all(),
                'side_names' => $selectedSideNames,
                'side_names_ar' => $selectedSideNamesAr,
            ];            
        }
    
        // Update the session with the new cart
        session()->put($request->cartkey, $cart);

        $totalItems = $this->getTotalItems($request->cartkey);

            return response()->json([
                'success' => true,
                'cart' => $cart,
                'total_items' => $totalItems,
            ]);
     
    }


    
    public function removeFromCart(Request $request)
    {
        $cart = session()->get($request->cartkey, []);

        $lineKey = $request->input('line_key', $request->id);

        if (isset($cart[$lineKey])) {
            // Remove the item from the cart
            unset($cart[$lineKey]);
        }
    
        // Update the session
        session()->put($request->cartkey, $cart);
    
        $totalItems = $this->getTotalItems($request->cartkey);
    
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_items' => $totalItems,
        ]);
    }
    



    public function getCart(Request $request)
    {
        $cart = session()->get($request->cartkey, []);

        return response()->json([
            'cart' => $cart,
        ]);
    }

    public function clearCart(Request $request)
    {
        session()->forget($request->cartkey);

        return response()->json([
            'success' => true,
            'cart' => [],
        ]);

    }

    public function updateCartQuantity(Request $request)
    {
        $cart = session()->get($request->cartkey, []);
        $lineKey = $request->input('line_key', $request->input('id'));
        $quantity = $request->input('quantity');

        if (isset($cart[$lineKey])) {
            $cart[$lineKey]['quantity'] = $quantity;
            session()->put($request->cartkey, $cart);
        }
    
        $totalItems = $this->getTotalItems($request->cartkey);
    
        return response()->json(['success' => true, 'cart' => $cart, 'total_items' => $totalItems]);
    }
    
    

    public function getTotalItems($cartkey)
    {
        // Retrieve the cart from the session
        $cart = session()->get($cartkey, []);
    
        // Calculate the total number of items in the cart
        $totalItems = 0;
        foreach ($cart as $item) {
            // Ensure the item has a 'quantity' key
            if (isset($item['quantity'])) {
                $totalItems += $item['quantity'];
            }
        }
        return $totalItems;
 
    }
    
       
}
