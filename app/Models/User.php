<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password_hash',
        'role',
        'telegram_chat_id',
        'telegram_connected_at',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password_hash'           => 'hashed',
            'telegram_connected_at'   => 'datetime',
            'is_active'               => 'boolean',
        ];
    }

    /**
     * Override Laravel auth to use password_hash column.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_user_id');
    }
}
