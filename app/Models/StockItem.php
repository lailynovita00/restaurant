<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'current_quantity',
        'unit',
    ];

    public static function unitOptions(): array
    {
        return [
            'gram' => 'Gram (g)',
            'kilogram' => 'Kilogram (kg)',
            'milliliter' => 'Milliliter (ml)',
            'liter' => 'Liter (L)',
            'piece' => 'Piece / Pcs',
            'pack' => 'Pack',
            'bottle' => 'Bottle',
            'can' => 'Can',
            'bag' => 'Bag',
            'box' => 'Box',
            'tray' => 'Tray',
        ];
    }

    protected $casts = [
        'current_quantity' => 'decimal:3',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_stock_items')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getUnitLabelAttribute(): string
    {
        return static::unitOptions()[$this->unit] ?? $this->unit;
    }
}