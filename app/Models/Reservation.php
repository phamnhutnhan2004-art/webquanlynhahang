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
        'table_id',
        'employee_id',
        'reservation_code',
        'reservation_time',
        'number_of_guests',
        'note',
        'source',
        'status',
    ];

    protected $casts = ['reservation_time' => 'datetime'];

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
