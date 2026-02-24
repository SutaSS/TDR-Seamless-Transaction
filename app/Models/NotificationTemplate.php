<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplate extends Model
{
    // TODO [PHASE 3 - Syahru]: Define $fillable sesuai kolom tabel notification_templates
    protected $fillable = [];

    // TODO [PHASE 3 - Syahru]: Scope template aktif
    public function scopeActive($query)
    {
        // TODO: return $query->where('is_active', true);
    }

    // TODO [PHASE 3 - Syahru]: Relasi ke User pembuat
    public function createdBy(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
