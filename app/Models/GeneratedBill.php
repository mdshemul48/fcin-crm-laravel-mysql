<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'generated_date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
