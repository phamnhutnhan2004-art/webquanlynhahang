<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenOrder extends Model
{
    public const STATUSES = ['pending', 'cooking', 'completed', 'served'];

    protected $fillable = ['order_id', 'staff_id', 'chef_id', 'status'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'staff_id');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'chef_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KitchenOrderItem::class);
    }

    public function refreshStatusFromItems(): void
    {
        $statuses = $this->items()->pluck('status');

        $status = match (true) {
            $statuses->isNotEmpty() && $statuses->every(fn (string $itemStatus) => $itemStatus === 'served') => 'served',
            $statuses->isNotEmpty() && $statuses->every(fn (string $itemStatus) => in_array($itemStatus, ['completed', 'served'], true)) => 'completed',
            $statuses->contains('cooking') => 'cooking',
            default => 'pending',
        };

        $this->update(['status' => $status]);
    }
}
