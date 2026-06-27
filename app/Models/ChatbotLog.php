<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    protected $fillable = [
        'customer_id',
        'reservation_id',
        'order_id',
        'session_id',
        'sender',
        'message',
        'intent',
        'confidence',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'metadata',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'metadata' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
