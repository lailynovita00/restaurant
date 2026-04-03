<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\StockItem;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image', 'video_url', 'is_hidden', 'category_id', 'name_ar', 'description_ar'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function stockItems()
    {
        return $this->belongsToMany(StockItem::class, 'menu_stock_items')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function availableSauces()
    {
        return $this->category ? $this->category->sauces() : Sauce::query()->whereRaw('1 = 0');
    }

    public function availableSides()
    {
        return $this->category ? $this->category->sides() : Side::query()->whereRaw('1 = 0');
    }

    public function getImageUrlAttribute(): string
    {
        $relativePath = ltrim((string) $this->image, '/');

        if ($relativePath === '') {
            return '';
        }

        if (is_file(public_path('storage/' . $relativePath))) {
            return asset('storage/' . $relativePath);
        }

        if (is_file(public_path($relativePath))) {
            return asset($relativePath);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            return route('media.public', ['path' => $relativePath]);
        }

        return asset('storage/' . $relativePath);
    }

    public function getVideoEmbedUrlAttribute(): string
    {
        $url = trim((string) $this->video_url);

        if ($url === '') {
            return '';
        }

        // youtu.be/{id}
        if (preg_match('~youtu\.be/([A-Za-z0-9_-]{11})~', $url, $match)) {
            return 'https://www.youtube.com/embed/' . $match[1];
        }

        // youtube.com/watch?v={id}
        if (preg_match('~[?&]v=([A-Za-z0-9_-]{11})~', $url, $match)) {
            return 'https://www.youtube.com/embed/' . $match[1];
        }

        // youtube.com/embed/{id}
        if (preg_match('~/embed/([A-Za-z0-9_-]{11})~', $url, $match)) {
            return 'https://www.youtube.com/embed/' . $match[1];
        }

        // drive.google.com/file/d/{id}/...
        if (preg_match('~/file/d/([^/]+)~', $url, $match)) {
            return 'https://drive.google.com/file/d/' . $match[1] . '/preview';
        }

        return $url;
    }
}
