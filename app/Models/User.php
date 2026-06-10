<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'role_id',
        'full_name',
        'email',
        'phone',
        'address',
        'password',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function isAdmin(): bool
    {
        return (int) $this->role_id === 1;
    }

    public function isStaff(): bool
    {
        return (int) $this->role_id === 2;
    }

    public function isCustomer(): bool
    {
        return (int) $this->role_id === 3;
    }
}
