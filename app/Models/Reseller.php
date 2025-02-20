<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reseller extends Model
{
    protected $fillable = ['name', 'phone', 'location', 'notes'];

    public function recharges(): HasMany
    {
        return $this->hasMany(ResellerRecharge::class);
    }

    public function getCurrentMonthRechargesAttribute()
    {
        return $this->recharges()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function getCurrentMonthCommissionAttribute()
    {
        return $this->recharges()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission');
    }
}
