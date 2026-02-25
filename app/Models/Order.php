<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{

    protected $fillable = [
        'order_number',
        'customer_id',
        'affiliate_id',
        'subtotal',
        'commission_amount',
        'shipping_cost',
        'total_amount',
        'status',
        'payment_method',
        'midtrans_transaction_id',
        'midtrans_snap_token',
        'payment_verified_at',
        'shipping_address',
        'shipping_courier',
        'shipping_tracking_number',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'subtotal'            => 'decimal:2',
        'commission_amount'   => 'decimal:2',
        'shipping_cost'       => 'decimal:2',
        'total_amount'        => 'decimal:2',
        'payment_verified_at' => 'datetime',
        'shipped_at'          => 'datetime',
        'completed_at'        => 'datetime',
        'cancelled_at'        => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_verified_at !== null;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(AffiliateCommission::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
