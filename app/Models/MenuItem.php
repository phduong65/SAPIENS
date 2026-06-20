<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_category_id', 'name_en', 'name_vi',
        'description_en', 'description_vi', 'price',
        'image_path', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return asset('images/menu-placeholder.svg');
        }

        // Paths starting with 'images/' live directly in public/
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        // Uploaded files go through storage/app/public
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }

        return asset('images/menu-placeholder.svg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price) . ',000đ';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
