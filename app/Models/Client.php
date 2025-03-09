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
        'bill_amount',
        'billing_status',
        'current_balance',
        'due_amount',
        'status',
        'remarks',
        'created_by',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function generatedBills()
    {
        return $this->hasMany(GeneratedBill::class);
    }

    public function balanceAdjustments()
    {
        return $this->hasMany(BalanceAdjustment::class);
    }
}
