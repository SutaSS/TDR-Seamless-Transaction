<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'referral_code',
        'status',
        'commission_rate',
        'payout_method',
        'payout_account_name',
        'payout_account_number',
        'total_clicks',
        'total_conversions',
        'total_commission_amount',
    ];

    protected $casts = [
        'commission_rate'          => 'decimal:2',
        'total_commission_amount'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referralClicks(): HasMany
    {
        return $this->hasMany(AffiliateReferralClick::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(AffiliateConversion::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
