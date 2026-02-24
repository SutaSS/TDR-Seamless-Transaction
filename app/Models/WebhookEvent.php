<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    public $timestamps = false;

    // TODO [PHASE 1 - Andika]: Define $fillable sesuai kolom tabel webhook_events
    protected $fillable = [];

    // TODO [PHASE 1 - Andika]: Cast kolom enum & boolean
    protected $casts = [];
}
