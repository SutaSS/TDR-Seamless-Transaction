<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateReferralClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'affiliate_id', 'referral_code_snapshot', 'session_key',
        'anonymized_ip', 'user_agent', 'landing_url',
        'is_attributed', 'expires_at',
    ];

    protected $casts = [
        'is_attributed' => 'boolean',
        'expires_at'    => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'referral_click_id');
    }
}
