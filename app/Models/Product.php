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

        return match ($this->slug) {
            'ga-xao-cay' => asset('images/ga-xao-cay.png'),
            'ca-chep-sot-cai-xanh' => asset('images/ca-chep-sot-cai-xanh.png'),
            default => asset('images/restaurant-interior.png'),
        };
    }
}
