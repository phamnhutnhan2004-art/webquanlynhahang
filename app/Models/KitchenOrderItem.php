<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenOrderItem extends Model
{
    public const STATUSES = ['pending', 'cooking', 'completed', 'served'];

    protected $fillable = ['kitchen_order_id', 'food_id', 'quantity', 'status'];

    public function kitchenOrder(): BelongsTo
    {
        return $this->belongsTo(KitchenOrder::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'food_id');
    }
}
