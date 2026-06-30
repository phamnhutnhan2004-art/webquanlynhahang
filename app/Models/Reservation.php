<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'customer_type',
        'table_id',
        'employee_id',
        'reservation_code',
        'reservation_time',
        'number_of_guests',
        'note',
        'source',
        'status',
        'confirmation_sent_at',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
        'confirmation_sent_at' => 'datetime',
    ];

    public function customerName(): string
    {
        return $this->customer?->full_name ?: (string) $this->guest_name;
    }

    public function customerPhone(): string
    {
        return $this->customer?->phone ?: (string) $this->guest_phone;
    }

    public function customerEmail(): ?string
    {
        return $this->customer?->email ?: $this->guest_email;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function chatbotLogs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class);
    }
}
