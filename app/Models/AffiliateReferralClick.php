<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateReferralClick extends Model
{
    public $timestamps = false;

    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel affiliate_referral_clicks
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Relasi ke Affiliate
    public function affiliate(): BelongsTo
    {
        // TODO: return $this->belongsTo(Affiliate::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke Orders (attributed orders)
    public function orders(): HasMany
    {
        // TODO: return $this->hasMany(Order::class, 'referral_click_id');
    }
}
