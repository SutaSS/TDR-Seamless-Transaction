<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfig extends Model
{
    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel system_configs
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast is_secret ke boolean
    protected $casts = [];

    // TODO [PHASE 1 - Andika]: Relasi ke User yang update
    public function updatedBy(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
