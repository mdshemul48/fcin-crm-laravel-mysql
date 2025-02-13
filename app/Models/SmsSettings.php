<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSettings extends Model
{
    protected $fillable = [
        'gateway_name',
        'api_key',
        'secret_key',
        'caller_id',
        'client_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
