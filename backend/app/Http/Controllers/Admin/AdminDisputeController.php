<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDisputeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $disputes = Dispute::with(['reservation.villa:id,title', 'raisedBy:id,name', 'against:id,name'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json($disputes);
    }

    public function show(Dispute $dispute): JsonResponse
    {
        $dispute->load(['reservation.villa', 'reservation.traveler', 'raisedBy', 'against', 'resolver']);
        return response()->json($dispute);
    }

    public function resolve(Request $request, Dispute $dispute): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|string|min:20',
            'admin_notes' => 'nullable|string',
        ]);

        $dispute->update([
            'status' => 'resolved',
            'resolution' => $request->resolution,
            'admin_notes' => $request->admin_notes,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        return response()->json(['message' => 'Litige résolu.', 'dispute' => $dispute]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'against' => 'required|exists:users,id',
            'type' => 'required|in:damage,payment,behavior,other',
            'description' => 'required|string|min:30',
        ]);

        $dispute = Dispute::create([
            'reservation_id' => $data['reservation_id'],
            'raised_by' => $request->user()->id,
            'against' => $data['against'],
            'type' => $data['type'],
            'description' => $data['description'],
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Litige ouvert.', 'dispute' => $dispute], 201);
    }
}
