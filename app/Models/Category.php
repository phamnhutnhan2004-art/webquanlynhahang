<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'status'];

    public function getMenuSlugAttribute(): string
    {
        return Str::slug($this->name) ?: (string) $this->id;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
