<?php
// ==============================
// Payment.php
// ==============================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'traveler_id',
        'amount',
        'currency',
        'method',         // stripe, paypal
        'status',         // pending, completed, failed, refunded
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'refund_id',
        'refund_amount',
        'refunded_at',
        'metadata',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refunded_at'   => 'datetime',
        'metadata'      => 'array',
    ];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function traveler()    { return $this->belongsTo(User::class, 'traveler_id'); }
}