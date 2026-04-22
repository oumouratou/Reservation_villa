<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'bio',
        'provider',
        'provider_id',
        'is_active',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    // Roles constants
    const ROLE_ADMIN    = 'admin';
    const ROLE_OWNER    = 'owner';
    const ROLE_CLIENT   = 'client';
    const ROLE_TRAVELER = 'traveler';

    public function isAdmin(): bool    { return $this->role === self::ROLE_ADMIN; }
    public function isOwner(): bool    { return $this->role === self::ROLE_OWNER; }
    public function isClient(): bool   { return $this->role === self::ROLE_CLIENT; }
    public function isTraveler(): bool { return $this->role === self::ROLE_TRAVELER; }

    // Relations
    public function villas()
    {
        return $this->hasMany(Villa::class, 'owner_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'traveler_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'traveler_id');
    }
}