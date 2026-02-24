<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateConversion extends Model
{
    protected $fillable = [
        'affiliate_id', 'order_id', 'referral_click_id',
        'commission_rate', 'commission_amount',
        'is_self_referral', 'status', 'approved_at', 'paid_at',
    ];

    protected $casts = [
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'is_self_referral'  => 'boolean',
        'approved_at'       => 'datetime',
        'paid_at'           => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function referralClick(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferralClick::class);
    }
}
