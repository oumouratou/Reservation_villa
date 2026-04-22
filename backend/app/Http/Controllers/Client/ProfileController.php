<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated client's profile.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated client's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'telephone' => 'sometimes|string|max:20',
        ]);

        // We need to handle 'name' vs 'nom' and 'prenom'
        if (isset($validatedData['nom']) && isset($validatedData['prenom'])) {
            $validatedData['name'] = $validatedData['prenom'] . ' ' . $validatedData['nom'];
            unset($validatedData['nom']);
            unset($validatedData['prenom']);
        } else if (isset($validatedData['nom'])) {
            $nameParts = explode(' ', $user->name);
            $validatedData['name'] = ($nameParts[0] ?? '') . ' ' . $validatedData['nom'];
            unset($validatedData['nom']);
        } else if (isset($validatedData['prenom'])) {
            $nameParts = explode(' ', $user->name);
            $validatedData['name'] = $validatedData['prenom'] . ' ' . ($nameParts[1] ?? '');
            unset($validatedData['prenom']);
        }
        
        if(isset($validatedData['telephone'])) {
            $validatedData['phone'] = $validatedData['telephone'];
            unset($validatedData['telephone']);
        }

        $user->update($validatedData);

        return response()->json($user);
    }
}
