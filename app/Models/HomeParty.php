<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeParty extends Model
{
    public const STATUSES = [
        'chờ xác nhận',
        'đã xác nhận',
        'đang chuẩn bị',
        'đang phục vụ',
        'hoàn thành',
        'đã hủy',
    ];

    protected $fillable = [
        'customer_id',
        'assigned_employee_id',
        'full_name',
        'phone',
        'email',
        'address',
        'event_date',
        'event_time',
        'guest_quantity',
        'party_type',
        'note',
        'total_price',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(HomePartyDetail::class);
    }
}
