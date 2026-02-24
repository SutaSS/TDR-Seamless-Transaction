<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'gateway_provider', 'external_id', 'gateway_invoice_id',
        'payment_method', 'invoice_url', 'amount', 'status',
        'signature_valid', 'raw_payload', 'webhook_received_at', 'paid_at',
    ];

    protected $casts = [
        'amount'               => 'decimal:2',
        'signature_valid'      => 'boolean',
        'webhook_received_at'  => 'datetime',
        'paid_at'              => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
