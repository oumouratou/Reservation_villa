<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DisputeController extends Controller
{
    /**
     * Owner: list disputes linked to owner reservations or directly involving owner.
     */
    public function myDisputes(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $disputes = Dispute::query()
            ->where(function ($query) use ($userId) {
                $query->where('raised_by', $userId)
                    ->orWhere('against', $userId)
                    ->orWhereHas('reservation.villa', fn($q) => $q->where('owner_id', $userId));
            })
            ->with([
                'reservation:id,villa_id,traveler_id,check_in,check_out,status,total_price',
                'reservation.villa:id,title,owner_id',
                'raisedBy:id,name,email',
                'against:id,name,email',
            ])
            ->latest()
            ->paginate(12);

        return response()->json($disputes);
    }

    /**
     * Owner/traveler involved in reservation can open dispute.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'type' => ['required', Rule::in(['damage', 'payment', 'behavior', 'other'])],
            'description' => 'required|string|min:10|max:2000',
        ]);

        $reservation = Reservation::with('villa:id,owner_id')->findOrFail($data['reservation_id']);
        $userId = $request->user()->id;
        $ownerId = $reservation->villa->owner_id;
        $travelerId = $reservation->traveler_id;

        abort_unless(in_array($userId, [$ownerId, $travelerId], true), 403);

        $dispute = Dispute::create([
            'reservation_id' => $reservation->id,
            'raised_by' => $userId,
            'against' => $userId === $ownerId ? $travelerId : $ownerId,
            'type' => $data['type'],
            'description' => $data['description'],
            'status' => 'open',
        ]);

        $dispute->load([
            'reservation:id,villa_id,traveler_id,check_in,check_out,status,total_price',
            'reservation.villa:id,title,owner_id',
            'raisedBy:id,name,email',
            'against:id,name,email',
        ]);

        return response()->json(['message' => 'Reclamation creee.', 'dispute' => $dispute], 201);
    }

    /**
     * Only author can edit dispute while it is not resolved.
     */
    public function update(Request $request, Dispute $dispute): JsonResponse
    {
        abort_unless($dispute->raised_by === $request->user()->id, 403);
        abort_if(in_array($dispute->status, ['resolved', 'closed'], true), 422, 'Reclamation deja cloturee.');

        $data = $request->validate([
            'type' => ['sometimes', Rule::in(['damage', 'payment', 'behavior', 'other'])],
            'description' => 'sometimes|string|min:10|max:2000',
            'status' => ['sometimes', Rule::in(['open', 'in_review'])],
        ]);

        $dispute->update($data);

        return response()->json(['message' => 'Reclamation mise a jour.', 'dispute' => $dispute->fresh()]);
    }

    /**
     * Only author can delete an open dispute.
     */
    public function destroy(Request $request, Dispute $dispute): JsonResponse
    {
        abort_unless($dispute->raised_by === $request->user()->id, 403);
        abort_if($dispute->status !== 'open', 422, 'Seules les reclamations ouvertes peuvent etre supprimees.');

        $dispute->delete();

        return response()->json(['message' => 'Reclamation supprimee.']);
    }
}