<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MenuGallery extends Model
{
    protected $fillable = ['title', 'description', 'image'];

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->exists($this->image)
            ? Storage::url($this->image)
            : asset($this->image);
    }
}
