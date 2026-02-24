<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel products
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast kolom decimal & boolean
    protected $casts = [];

    // TODO [PHASE 1 - Andika]: Scope hanya produk aktif
    public function scopeActive($query)
    {
        // TODO: return $query->where('is_active', true);
    }
}
