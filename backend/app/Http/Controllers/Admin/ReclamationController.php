<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use App\Notifications\ReclamationResponseReceived;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReclamationController extends Controller
{
    /**
     * Respond to a specific reclamation.
     */
    public function respond(Request $request, Reclamation $reclamation): JsonResponse
    {
        $validated = $request->validate([
            'reponse_agent' => 'required|string|max:2000',
            'statut' => 'required|in:en cours,traitee',
        ]);

        $reclamation->reponse_agent = $validated['reponse_agent'];
        $reclamation->statut = $validated['statut'];
        $reclamation->save();

        // Notify the client
        $reclamation->user->notify(new ReclamationResponseReceived($reclamation));

        return response()->json($reclamation);
    }
}
