<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel affiliates
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Relasi ke User
    public function user(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke referral clicks
    public function referralClicks(): HasMany
    {
        // TODO: return $this->hasMany(AffiliateReferralClick::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke conversions
    public function conversions(): HasMany
    {
        // TODO: return $this->hasMany(AffiliateConversion::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke orders
    public function orders(): HasMany
    {
        // TODO: return $this->hasMany(Order::class);
    }
}
