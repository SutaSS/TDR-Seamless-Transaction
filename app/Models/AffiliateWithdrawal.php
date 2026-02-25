<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateWithdrawal extends Model
{
    /**
     * Kolom sesuai migrasi: affiliate_withdrawals table.
     * Status: pending → processing → completed | rejected
     */
    protected $fillable = [
        'affiliate_id',
        'amount',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'processed_at',
        'processed_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // ──────────────────────── Relations ────────────────────────

    /**
     * User afiliasi yang mengajukan penarikan.
     * (FK affiliate_id → users.id)
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    /**
     * Admin yang memproses penarikan.
     * (FK processed_by → users.id)
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ──────────────────────── Scopes ──────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}