<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'conversion_id', 'event_type',
        'channel', 'recipient_chat_id_snapshot', 'template_key',
        'message_body', 'provider_message_id', 'status',
        'retry_count', 'last_error', 'sent_at',
    ];

    protected $casts = [
        'retry_count' => 'integer',
        'sent_at'     => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'queued');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(AffiliateConversion::class, 'conversion_id');
    }
}
