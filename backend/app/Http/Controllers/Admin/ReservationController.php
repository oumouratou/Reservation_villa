<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Notifications\ReservationStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    /**
     * Update the specified reservation's status.
     */
    public function updateStatus(Request $request, Reservation $reservation): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'commentaire_agent' => 'nullable|string|max:2000',
        ]);

        $reservation->status = $validated['status'];
        if (isset($validated['commentaire_agent'])) {
            $reservation->commentaire_agent = $validated['commentaire_agent'];
        }
        $reservation->save();

        // Notify the client
        $reservation->user->notify(new ReservationStatusUpdated($reservation));

        return response()->json($reservation);
    }
}
