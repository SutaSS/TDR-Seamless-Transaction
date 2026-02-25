<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateClick extends Model
{
    /**
     * Kolom sesuai migrasi: affiliate_clicks table.
     * Tabel hanya punya created_at (bukan updated_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'affiliate_id',
        'ip_address',
        'user_agent',
        'referrer_url',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    // ──────────────────────── Relations ────────────────────────

    /**
     * User afiliasi yang memiliki klik referral ini.
     * (FK affiliate_id → users.id)
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }
}