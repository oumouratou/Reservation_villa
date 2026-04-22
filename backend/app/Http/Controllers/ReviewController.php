<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function villaReviews(int $villa): JsonResponse
    {
        $reviews = Review::where('villa_id', $villa)
            ->where('is_approved', true)
            ->with('user:id,name,avatar')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id'     => 'required|exists:reservations,id',
            'rating'             => 'required|numeric|min:1|max:5',
            'cleanliness'        => 'required|numeric|min:1|max:5',
            'accuracy'           => 'required|numeric|min:1|max:5',
            'communication'      => 'required|numeric|min:1|max:5',
            'location'           => 'required|numeric|min:1|max:5',
            'value'              => 'required|numeric|min:1|max:5',
            'comment'            => 'required|string|min:10',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        abort_unless($reservation->traveler_id === $request->user()->id, 403);
        abort_unless($reservation->status === Reservation::STATUS_COMPLETED, 422, 'Vous ne pouvez noter qu\'une réservation terminée.');

        $existing = Review::where('reservation_id', $reservation->id)->exists();
        abort_if($existing, 422, 'Avis déjà soumis pour cette réservation.');

        $review = Review::create([
            'villa_id'       => $reservation->villa_id,
            'reservation_id' => $reservation->id,
            'user_id'        => $request->user()->id,
            'rating'         => $request->rating,
            'cleanliness'    => $request->cleanliness,
            'accuracy'       => $request->accuracy,
            'communication'  => $request->communication,
            'location'       => $request->location,
            'value'          => $request->value,
            'comment'        => $request->comment,
            'is_approved'    => true,
        ]);

        $review->load('user:id,name,avatar');
        return response()->json(['message' => 'Avis publié.', 'review' => $review], 201);
    }

    public function reply(Request $request, Review $review): JsonResponse
    {
        $request->validate(['owner_response' => 'required|string|min:3']);
        abort_unless($review->villa && $review->villa->owner_id === $request->user()->id, 403);

        $review->update(['owner_response' => $request->owner_response]);
        return response()->json(['message' => 'Réponse publiée.', 'review' => $review]);
    }

    public function moderate(Request $request, Review $review): JsonResponse
    {
        $request->validate(['is_approved' => 'required|boolean']);

        $review->update(['is_approved' => $request->boolean('is_approved')]);

        return response()->json([
            'message' => 'Avis modéré.',
            'review'  => $review,
        ]);
    }
}