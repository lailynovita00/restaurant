<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'requires_sauce', 'requires_side'];

    protected $casts = [
        'requires_sauce' => 'boolean',
        'requires_side' => 'boolean',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function sauces()
    {
        return $this->belongsToMany(Sauce::class)->withTimestamps();
    }

    public function sides()
    {
        return $this->belongsToMany(Side::class)->withTimestamps();
    }
}
