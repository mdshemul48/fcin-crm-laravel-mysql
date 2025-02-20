<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerRecharge extends Model
{
    protected $fillable = ['reseller_id', 'amount', 'commission', 'notes'];

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
