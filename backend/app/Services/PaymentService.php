<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;

class PaymentService
{
    public function createPaymentIntent(Reservation $reservation): object
    {
        return (object) [
            'id' => 'pi_local_' . uniqid(),
            'client_secret' => 'cs_local_' . uniqid(),
            'amount' => (int) round(((float) $reservation->total_price) * 100),
            'currency' => 'eur',
        ];
    }

    public function confirmPayment(Reservation $reservation, string $paymentIntentId): Payment
    {
        $payment = Payment::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'traveler_id' => $reservation->traveler_id,
                'amount' => $reservation->total_price,
                'currency' => 'eur',
                'method' => 'stripe',
                'status' => 'completed',
                'stripe_payment_intent_id' => $paymentIntentId,
            ]
        );

        $reservation->update(['payment_status' => Reservation::PAYMENT_PAID]);

        return $payment;
    }

    public function refund(Reservation $reservation): void
    {
        $payment = Payment::where('reservation_id', $reservation->id)->first();
        if (!$payment) {
            return;
        }

        $payment->update([
            'status' => 'refunded',
            'refund_amount' => $payment->amount,
            'refunded_at' => now(),
        ]);

        $reservation->update(['payment_status' => Reservation::PAYMENT_REFUNDED]);
    }

    public function handleSuccess(object $intent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent->id ?? '')->first();
        if ($payment) {
            $payment->update(['status' => 'completed']);
        }
    }

    public function handleFailure(object $intent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent->id ?? '')->first();
        if ($payment) {
            $payment->update(['status' => 'failed']);
        }
    }

    public function handleRefund(object $charge): void
    {
        $payment = Payment::where('stripe_charge_id', $charge->id ?? '')->first();
        if ($payment) {
            $payment->update([
                'status' => 'refunded',
                'refund_amount' => ((float) ($charge->amount_refunded ?? 0)) / 100,
                'refunded_at' => now(),
            ]);
        }
    }
}
