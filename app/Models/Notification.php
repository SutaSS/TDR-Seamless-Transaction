<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    // TODO [PHASE 3 - Syahru]: Define $fillable sesuai kolom tabel notifications
    protected $fillable = [];

    // TODO [PHASE 3 - Syahru]: Cast kolom enum & integer
    protected $casts = [];

    // TODO [PHASE 3 - Syahru]: Scope status pending (belum terkirim)
    public function scopePending($query)
    {
        // TODO: return $query->where('status', 'queued');
    }

    // TODO [PHASE 3 - Syahru]: Relasi ke User
    public function user(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class);
    }

    // TODO [PHASE 3 - Syahru]: Relasi ke Order
    public function order(): BelongsTo
    {
        // TODO: return $this->belongsTo(Order::class);
    }

    // TODO [PHASE 3 - Syahru]: Relasi ke AffiliateConversion
    public function conversion(): BelongsTo
    {
        // TODO: return $this->belongsTo(AffiliateConversion::class, 'conversion_id');
    }
}
