<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockItem;
use App\Models\Order;
use App\Models\Menu;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'order_id',
        'menu_id',
        'created_by_user_id',
        'movement_type',
        'quantity_change',
        'note',
    ];

    protected $casts = [
        'quantity_change' => 'decimal:3',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}