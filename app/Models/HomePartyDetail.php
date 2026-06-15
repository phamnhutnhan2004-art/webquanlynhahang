<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomePartyDetail extends Model
{
    protected $fillable = [
        'home_party_id',
        'food_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function homeParty(): BelongsTo
    {
        return $this->belongsTo(HomeParty::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'food_id');
    }
}
