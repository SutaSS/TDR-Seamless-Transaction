<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingLog extends Model
{
    /**
     * Kolom sesuai migrasi: tracking_logs table.
     * Tabel hanya punya created_at (bukan updated_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status_title',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ──────────────────────── Relations ────────────────────────

    /** Pesanan yang terkait dengan log ini. */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}