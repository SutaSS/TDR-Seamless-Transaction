<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_user_id', 'affiliate_id', 'referral_click_id',
        'subtotal_amount', 'discount_amount', 'total_amount', 'currency',
        'order_status', 'payment_status', 'tracking_number', 'shipping_provider',
        'customer_name', 'customer_phone', 'note', 'paid_at', 'delivered_at',
        'shipping_address', 'shipping_city', 'shipping_province',
        'shipping_postal_code', 'shipping_courier', 'shipping_cost',
        'status_changed_at',
    ];

    protected $casts = [
        'subtotal_amount'  => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'paid_at'          => 'datetime',
        'delivered_at'     => 'datetime',
        'status_changed_at'=> 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_user_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function conversion(): HasOne
    {
        return $this->hasOne(AffiliateConversion::class);
    }

    public function referralClick(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferralClick::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
