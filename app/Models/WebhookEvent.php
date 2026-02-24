<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source', 'event_id', 'external_id', 'payload_hash',
        'signature_valid', 'process_status', 'error_message',
        'raw_payload', 'received_at', 'processed_at',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'received_at'     => 'datetime',
        'processed_at'    => 'datetime',
    ];
}
