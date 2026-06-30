<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteMenuItem extends Model
{
    protected $fillable = ['label', 'url', 'route_name', 'icon', 'sort_order', 'is_visible', 'target'];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];
}
