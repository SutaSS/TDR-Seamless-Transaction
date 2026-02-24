<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateConversion extends Model
{
    // TODO [PHASE 2 - Ghufron]: Define $fillable sesuai kolom tabel affiliate_conversions
    protected $fillable = [];

    // TODO [PHASE 2 - Ghufron]: Cast kolom decimal & enum
    protected $casts = [];

    // TODO [PHASE 2 - Ghufron]: Relasi ke Affiliate
    public function affiliate(): BelongsTo
    {
        // TODO: return $this->belongsTo(Affiliate::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke Order
    public function order(): BelongsTo
    {
        // TODO: return $this->belongsTo(Order::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke ReferralClick
    public function referralClick(): BelongsTo
    {
        // TODO: return $this->belongsTo(AffiliateReferralClick::class);
    }
}
