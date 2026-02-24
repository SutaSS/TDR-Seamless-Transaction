<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    public $timestamps = false;

    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel order_status_histories
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Relasi ke Order
    public function order(): BelongsTo
    {
        // TODO: return $this->belongsTo(Order::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke User yang mengubah status
    public function changedBy(): BelongsTo
    {
        // TODO: return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
