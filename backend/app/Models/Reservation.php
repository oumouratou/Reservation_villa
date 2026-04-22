<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'traveler_id',
        'check_in',
        'check_out',
        'guests',
        'nights',
        'base_price',
        'cleaning_fee',
        'service_fee',
        'security_deposit',
        'total_price',
        'status',          // pending, approved, rejected, cancelled, completed
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'special_requests',
        'payment_status',  // unpaid, partial, paid, refunded
    ];

    protected $casts = [
        'check_in'          => 'date',
        'check_out'         => 'date',
        'cancelled_at'      => 'datetime',
        'base_price'        => 'decimal:2',
        'cleaning_fee'      => 'decimal:2',
        'service_fee'       => 'decimal:2',
        'security_deposit'  => 'decimal:2',
        'total_price'       => 'decimal:2',
        'guests'            => 'integer',
        'nights'            => 'integer',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const PAYMENT_UNPAID   = 'unpaid';
    const PAYMENT_PAID     = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    // Scopes
    public function scopePending($query)   { return $query->where('status', self::STATUS_PENDING); }
    public function scopeApproved($query)  { return $query->where('status', self::STATUS_APPROVED); }
    public function scopeCompleted($query) { return $query->where('status', self::STATUS_COMPLETED); }

    // Relations
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    public function traveler()
    {
        return $this->belongsTo(User::class, 'traveler_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }

    // Helpers
    public function isRefundable(): bool
    {
        $policy = $this->villa->cancellation_policy;
        $daysUntilCheckIn = now()->diffInDays($this->check_in, false);

        return match($policy) {
            'flexible'  => $daysUntilCheckIn >= 1,
            'moderate'  => $daysUntilCheckIn >= 5,
            'strict'    => $daysUntilCheckIn >= 14,
            default     => false,
        };
    }
}