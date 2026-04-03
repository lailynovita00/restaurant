<?php

namespace App\Http\Controllers\Traits;

use App\Models\Order;

trait OrderNumberGeneratorTrait
{
    protected function generateOrderNumber()
    {
        // Sequential order number (1, 2, 3, ...)
        // Start from next id-like sequence and skip existing values if needed.
        $order_no = ((int) Order::max('id')) + 1;

        while (Order::where('order_no', $order_no)->exists()) {
            $order_no++;
        }

        return $order_no;
    }
}
