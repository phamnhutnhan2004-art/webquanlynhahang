<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'image', 'status'];

    protected $casts = ['price' => 'decimal:2'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function kitchenItems(): HasMany
    {
        return $this->hasMany(KitchenOrderItem::class, 'food_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }

            if (Storage::disk('public')->exists($this->image)) {
                return Storage::url($this->image);
            }

            if (is_file(public_path($this->image))) {
                return asset($this->image);
            }
        }

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $extension) {
            $path = "images/{$this->slug}.{$extension}";

            if (is_file(public_path($path))) {
                return asset($path);
            }
        }

        return $this->fallbackImageUrl();
    }

    public function getFallbackImageUrlAttribute(): string
    {
        return $this->fallbackImageUrl();
    }

    private function fallbackImageUrl(): string
    {
        $slug = (string) $this->slug;

        if (str_contains($slug, 'ga') || str_contains($slug, 'cay') || str_contains($slug, 'ech')) {
            return asset('images/ga-xao-cay.png');
        }

        if (str_contains($slug, 'ca') || str_contains($slug, 'hai-san') || str_contains($slug, 'tom')
            || str_contains($slug, 'cua') || str_contains($slug, 'muc') || str_contains($slug, 'so')
            || str_contains($slug, 'lau') || str_contains($slug, 'ngheu')) {
            return asset('images/ca-chep-sot-cai-xanh.png');
        }

        return asset('images/restaurant-interior.png');
    }
}
