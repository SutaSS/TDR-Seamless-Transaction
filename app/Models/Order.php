<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel orders
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast kolom enum & decimal
    protected $casts = [];

    // TODO [PHASE 1 - Andika]: Relasi ke customer (User)
    public function customer(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class, 'customer_user_id');
    }

    // TODO [PHASE 1 - Andika]: Relasi ke Affiliate
    public function affiliate(): BelongsTo
    {
        // TODO: return $this->belongsTo(Affiliate::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke order items
    public function items(): HasMany
    {
        // TODO: return $this->hasMany(OrderItem::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke payment
    public function payment(): HasOne
    {
        // TODO: return $this->hasOne(Payment::class);
    }

    // TODO [PHASE 2 - Ghufron]: Relasi ke affiliate conversion
    public function conversion(): HasOne
    {
        // TODO: return $this->hasOne(AffiliateConversion::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke referral click
    public function referralClick(): BelongsTo
    {
        // TODO: return $this->belongsTo(AffiliateReferralClick::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke status histories
    public function statusHistories(): HasMany
    {
        // TODO: return $this->hasMany(OrderStatusHistory::class);
    }
}
