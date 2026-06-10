<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotHistory extends Model
{
    protected $fillable = ['customer_id', 'reservation_id', 'session_id', 'sender', 'message', 'intent', 'confidence'];

    protected $casts = ['confidence' => 'decimal:2'];
}
