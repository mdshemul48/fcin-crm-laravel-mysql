<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'adjusted_by',
        'adjustment_type',
        'old_current_balance',
        'new_current_balance',
        'old_due_amount',
        'new_due_amount',
        'remarks',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
