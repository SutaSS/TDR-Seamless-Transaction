<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel payments
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast kolom enum, decimal, boolean
    protected $casts = [];

    // TODO [PHASE 1 - Andika]: Relasi ke Order
    public function order(): BelongsTo
    {
        // TODO: return $this->belongsTo(Order::class);
    }
}
