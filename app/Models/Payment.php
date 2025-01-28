<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount_from_client_account',
        'amount',
        'payment_date',
        'payment_type',
        'discount',
        'month',
        'remarks',
        'collected_by',
        'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
