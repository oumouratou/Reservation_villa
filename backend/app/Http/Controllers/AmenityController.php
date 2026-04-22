<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $amenities = Amenity::query()
            ->where('owner_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        return response()->json($amenities);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);

        $amenity = Amenity::create([
            'owner_id' => $request->user()->id,
            'name' => $data['name'],
            'price' => $data['price'],
            'category' => $data['category'] ?? 'included',
            'icon' => $data['icon'] ?? null,
        ]);

        return response()->json(['message' => 'Equipement ajoute.', 'amenity' => $amenity], 201);
    }

    public function update(Request $request, Amenity $amenity): JsonResponse
    {
        abort_unless($amenity->owner_id === $request->user()->id, 403, 'This action is unauthorized.');

        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);

        $amenity->update($data);

        return response()->json(['message' => 'Equipement mis a jour.', 'amenity' => $amenity->fresh()]);
    }

    public function destroy(Request $request, Amenity $amenity): JsonResponse
    {
        abort_unless($amenity->owner_id === $request->user()->id, 403, 'This action is unauthorized.');

        $amenity->delete();

        return response()->json(['message' => 'Equipement supprime.']);
    }
}
