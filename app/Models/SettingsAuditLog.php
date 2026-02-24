<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingsAuditLog extends Model
{
    public $timestamps = false;

    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel settings_audit_logs
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Relasi ke User pelaku
    public function actor(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class, 'actor_user_id');
    }
}
