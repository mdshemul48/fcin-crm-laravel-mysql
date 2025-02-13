<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentSms extends Model
{
    protected $table = 'sent_sms';

    protected $fillable = [
        'client_id',
        'message_id',
        'content',
        'response',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
