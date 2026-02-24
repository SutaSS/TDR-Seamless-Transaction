<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'sku', 'name', 'description', 'price',
        'commission_rate', 'stock', 'is_active',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
