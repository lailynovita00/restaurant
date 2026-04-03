<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_name',
        'quantity',
        'subtotal',
        'sauce_name',
        'sauce_name_ar',
        'side_names',
        'side_names_ar',
    ];

    protected $casts = [
        'side_names' => 'array',
        'side_names_ar' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
