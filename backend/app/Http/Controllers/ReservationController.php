<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Villa;
use App\Services\PricingService;
use App\Events\ReservationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function __construct(private PricingService $pricing) {}

    /**
     * Traveler: create reservation.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'villa_id'        => 'required|exists:villas,id',
            'check_in'        => 'required|date|after_or_equal:today',
            'check_out'       => 'required|date|after:check_in',
            'guests'          => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $villa = Villa::approved()->findOrFail($data['villa_id']);

        // Validate guest count
        abort_if($data['guests'] > $villa->max_guests, 422, "Capacité maximale: {$villa->max_guests} personnes.");

        // Check availability
        $conflict = Reservation::where('villa_id', $villa->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('check_in', '<', $data['check_out'])
            ->where('check_out', '>', $data['check_in'])
            ->exists();

        abort_if($conflict, 409, 'Ces dates ne sont plus disponibles.');

        // Calculate price
        $pricing = $this->pricing->calculate($villa, $data['check_in'], $data['check_out']);

        $reservation = Reservation::create([
            'villa_id'        => $villa->id,
            'traveler_id'     => $request->user()->id,
            'check_in'        => $data['check_in'],
            'check_out'       => $data['check_out'],
            'guests'          => $data['guests'],
            'nights'          => $pricing['nights'],
            'base_price'      => $pricing['base_price'],
            'cleaning_fee'    => $pricing['cleaning_fee'],
            'service_fee'     => $pricing['service_fee'],
            'security_deposit' => $pricing['security_deposit'],
            'total_price'     => $pricing['total'],
            'status'          => Reservation::STATUS_PENDING,
            'payment_status'  => Reservation::PAYMENT_UNPAID,
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        $reservation->load(['villa:id,title,city', 'traveler:id,name,email']);

        // Notify villa owner
        $villa->owner->notify(new \App\Notifications\NewReservationRequest($reservation));

        return response()->json([
            'message'     => 'Demande de réservation envoyée.',
            'reservation' => $reservation,
            'pricing'     => $pricing,
        ], 201);
    }

    /**
     * Traveler: list my reservations.
     */
    public function myReservations(Request $request): JsonResponse
    {
        $reservations = Reservation::where('traveler_id', $request->user()->id)
            ->with(['villa:id,title,city,price_per_night', 'villa.photos' => fn($q) => $q->where('is_cover', true)])
            ->latest()
            ->paginate(10);

        return response()->json($reservations);
    }

    /**
     * Get single reservation.
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);
        $reservation->load(['villa.owner:id,name,avatar', 'villa.photos', 'traveler:id,name', 'payment', 'review']);
        return response()->json($reservation);
    }

    /**
     * Owner: list villa reservations.
     */
    public function villaReservations(Request $request): JsonResponse
    {
        $villaIds = $request->user()->villas()->pluck('id');

        $reservations = Reservation::whereIn('villa_id', $villaIds)
            ->with(['villa:id,title', 'traveler:id,name,email,avatar'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return response()->json($reservations);
    }

    /**
     * Owner: approve reservation.
     */
    public function approve(Request $request, Reservation $reservation): JsonResponse
    {
        abort_unless($reservation->villa->owner_id === $request->user()->id, 403);
        abort_unless($reservation->status === Reservation::STATUS_PENDING, 422, 'Réservation déjà traitée.');

        $reservation->update(['status' => Reservation::STATUS_APPROVED]);

        event(new ReservationStatusChanged($reservation));

        $reservation->traveler->notify(new \App\Notifications\ReservationConfirmed($reservation));

        return response()->json(['message' => 'Réservation approuvée.', 'reservation' => $reservation]);
    }

    /**
     * Owner: reject reservation.
     */
    public function reject(Request $request, Reservation $reservation): JsonResponse
    {
        abort_unless($reservation->villa->owner_id === $request->user()->id, 403);
        abort_unless($reservation->status === Reservation::STATUS_PENDING, 422);

        $request->validate(['reason' => 'required|string']);
        $reservation->update([
            'status'               => Reservation::STATUS_REJECTED,
            'cancellation_reason'  => $request->reason,
            'cancelled_by'         => $request->user()->id,
            'cancelled_at'         => now(),
        ]);

        $reservation->traveler->notify(new \App\Notifications\ReservationRejected($reservation));

        return response()->json(['message' => 'Réservation rejetée.']);
    }

    /**
     * Traveler/Owner: cancel reservation.
     */
    public function cancel(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('cancel', $reservation);
        $request->validate(['reason' => 'nullable|string']);

        $reservation->update([
            'status'              => Reservation::STATUS_CANCELLED,
            'cancellation_reason' => $request->reason,
            'cancelled_by'        => $request->user()->id,
            'cancelled_at'        => now(),
        ]);

        // Trigger refund if paid
        if ($reservation->payment_status === Reservation::PAYMENT_PAID && $reservation->isRefundable()) {
            app(\App\Services\PaymentService::class)->refund($reservation);
        }

        return response()->json(['message' => 'Réservation annulée.']);
    }

    /**
     * Get price quote before booking.
     */
    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'villa_id'  => 'required|exists:villas,id',
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $villa = Villa::approved()->findOrFail($data['villa_id']);
        $pricing = $this->pricing->calculate($villa, $data['check_in'], $data['check_out']);

        return response()->json($pricing);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with('villa:id,name,address,city') // Eager load villa details
            ->where('user_id', Auth::id())
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($reservations);
    }
}