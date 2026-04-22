<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Create Stripe Payment Intent.
     */
    public function createIntent(Request $request): JsonResponse
    {
        $request->validate(['reservation_id' => 'required|exists:reservations,id']);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $this->authorize('pay', $reservation);

        abort_unless(in_array($reservation->status, ['pending', 'approved']), 422, 'Réservation non payable dans ce statut.');
        abort_unless($reservation->payment_status === Reservation::PAYMENT_UNPAID, 422, 'Déjà payée.');

        $intent = $this->paymentService->createPaymentIntent($reservation);

        return response()->json([
            'client_secret'       => $intent->client_secret,
            'payment_intent_id'   => $intent->id,
            'amount'              => $intent->amount,
            'currency'            => $intent->currency,
        ]);
    }

    /**
     * Confirm payment after Stripe success.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id'     => 'required|exists:reservations,id',
            'payment_intent_id'  => 'required|string',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $this->authorize('pay', $reservation);

        $payment = $this->paymentService->confirmPayment($reservation, $request->payment_intent_id);

        $reservation->traveler->notify(new \App\Notifications\PaymentReceived($reservation));

        return response()->json([
            'message' => 'Paiement confirmé.',
            'payment' => $payment,
        ]);
    }

    /**
     * Stripe webhook endpoint.
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, config('services.stripe.webhook_secret'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match($event->type) {
            'payment_intent.succeeded'       => $this->paymentService->handleSuccess($event->data->object),
            'payment_intent.payment_failed'  => $this->paymentService->handleFailure($event->data->object),
            'charge.refunded'                => $this->paymentService->handleRefund($event->data->object),
            default                          => null,
        };

        return response()->json(['received' => true]);
    }

    /**
     * Owner: get earnings summary.
     */
    public function ownerEarnings(Request $request): JsonResponse
    {
        $villaIds = $request->user()->villas()->pluck('id');

        $payments = Payment::whereHas('reservation', fn($q) => $q->whereIn('villa_id', $villaIds))
            ->where('status', 'completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalEarnings = Payment::whereHas('reservation', fn($q) => $q->whereIn('villa_id', $villaIds))
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'total_earnings' => $totalEarnings,
            'monthly'        => $payments,
        ]);
    }

    /**
     * Traveler: payment history.
     */
    public function myPayments(Request $request): JsonResponse
    {
        $payments = Payment::where('traveler_id', $request->user()->id)
            ->with(['reservation.villa:id,title,city'])
            ->latest()
            ->paginate(10);

        return response()->json($payments);
    }
}