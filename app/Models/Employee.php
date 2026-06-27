<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = ['user_id', 'employee_code', 'position', 'shift', 'salary', 'hire_date', 'status'];

    protected $casts = ['salary' => 'decimal:2', 'hire_date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function staffKitchenOrders(): HasMany
    {
        return $this->hasMany(KitchenOrder::class, 'staff_id');
    }

    public function chefKitchenOrders(): HasMany
    {
        return $this->hasMany(KitchenOrder::class, 'chef_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'cashier_id');
    }
}
