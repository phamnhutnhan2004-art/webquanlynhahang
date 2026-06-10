<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    protected $fillable = ['category_id', 'food_name', 'slug', 'description', 'price', 'image', 'spicy_level', 'preparation_time', 'status'];

    protected $casts = ['price' => 'decimal:2'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
