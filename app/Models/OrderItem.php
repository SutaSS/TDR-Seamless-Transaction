<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel order_items
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast kolom decimal
    protected $casts = [];

    // TODO [PHASE 1 - Andika]: Relasi ke Order
    public function order(): BelongsTo
    {
        // TODO: return $this->belongsTo(Order::class);
    }

    // TODO [PHASE 1 - Andika]: Relasi ke Product (nullable)
    public function product(): BelongsTo
    {
        // TODO: return $this->belongsTo(Product::class);
    }
}
