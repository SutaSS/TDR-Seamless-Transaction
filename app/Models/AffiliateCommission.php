<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    /**
     * Kolom sesuai migrasi: affiliate_commissions table.
     * Status: pending → earned → withdrawn
     */
    protected $fillable = [
        'order_id',
        'affiliate_id',
        'amount',
        'commission_rate',
        'status',
        'earned_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'earned_at'       => 'datetime',
    ];

    // ──────────────────────── Relations ────────────────────────

    /** Order yang menghasilkan komisi ini. */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * User afiliasi pemilik komisi.
     * (FK affiliate_id → users.id)
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    // ──────────────────────── Scopes ──────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeEarned($query)
    {
        return $query->where('status', 'earned');
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('status', 'withdrawn');
    }
}