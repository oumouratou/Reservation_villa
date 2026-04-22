<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Reclamation;

class ReclamationController extends Controller
{
    /**
     * Display a listing of the client's reclamations.
     */
    public function index(): JsonResponse
    {
        $reclamations = Reclamation::where('user_id', Auth::id())
            ->with('reservation:id,start_date,end_date,villa_id') // Include basic reservation info
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reclamations);
    }

    /**
     * Store a newly created reclamation in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'sujet' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ]);

        // Verify that the reservation belongs to the authenticated user
        $reservation = Auth::user()->reservations()->findOrFail($validated['reservation_id']);

        $reclamation = Reclamation::create([
            'user_id' => Auth::id(),
            'reservation_id' => $reservation->id,
            'sujet' => $validated['sujet'],
            'description' => $validated['description'],
            'statut' => 'ouverte',
        ]);

        return response()->json($reclamation, 201);
    }
}
