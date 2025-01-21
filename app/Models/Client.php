<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'username',
        'phone_number',
        'address',
        'package_id',
        'current_balance',
        'due',
        'bill_amount',
        'created_by',
        'disabled'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
