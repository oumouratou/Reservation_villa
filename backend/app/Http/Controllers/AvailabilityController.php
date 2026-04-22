<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    /**
     * Get villa availability for a month range (public).
     */
    public function show(Request $request, Villa $villa): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after:from',
        ]);

        $from = Carbon::parse($request->from)->startOfMonth();
        $to   = Carbon::parse($request->to)->endOfMonth();

        // Custom availability overrides
        $overrides = Availability::where('villa_id', $villa->id)
            ->whereBetween('date', [$from, $to])
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        // Booked dates from reservations
        $bookedDates = \App\Models\Reservation::where('villa_id', $villa->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('check_in', '<=', $to)
            ->where('check_out', '>=', $from)
            ->get()
            ->flatMap(function ($res) {
                $dates = [];
                $current = Carbon::parse($res->check_in);
                $end = Carbon::parse($res->check_out);
                while ($current->lt($end)) {
                    $dates[] = $current->format('Y-m-d');
                    $current->addDay();
                }
                return $dates;
            })
            ->unique()
            ->values();

        // Build calendar
        $calendar = [];
        $current = $from->copy();
        while ($current->lte($to)) {
            $dateStr = $current->format('Y-m-d');
            $override = $overrides->get($dateStr);
            $isBooked = $bookedDates->contains($dateStr);

            $calendar[] = [
                'date'         => $dateStr,
                'is_available' => $override ? (bool)$override->is_available && !$isBooked : !$isBooked,
                'is_booked'    => $isBooked,
                'price'        => $override?->custom_price ?? $villa->getPriceForDate($current),
            ];

            $current->addDay();
        }

        return response()->json($calendar);
    }

    /**
     * Owner: bulk update availability.
     */
    public function update(Request $request, Villa $villa): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403);

        $request->validate([
            'dates'             => 'required|array',
            'dates.*.date'      => 'required|date',
            'dates.*.available' => 'required|boolean',
            'dates.*.price'     => 'nullable|numeric|min:0',
            'dates.*.note'      => 'nullable|string|max:100',
        ]);

        foreach ($request->dates as $item) {
            Availability::updateOrCreate(
                ['villa_id' => $villa->id, 'date' => $item['date']],
                [
                    'is_available' => $item['available'],
                    'custom_price' => $item['price'] ?? null,
                    'note'         => $item['note'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Disponibilités mises à jour.']);
    }

    /**
     * Owner: block a date range.
     */
    public function blockRange(Request $request, Villa $villa): JsonResponse
    {
        abort_unless($villa->owner_id === $request->user()->id, 403);

        $request->validate([
            'from'   => 'required|date|after_or_equal:today',
            'to'     => 'required|date|after:from',
            'reason' => 'nullable|string|max:100',
        ]);

        $current = Carbon::parse($request->from);
        $end     = Carbon::parse($request->to);

        while ($current->lte($end)) {
            Availability::updateOrCreate(
                ['villa_id' => $villa->id, 'date' => $current->format('Y-m-d')],
                ['is_available' => false, 'note' => $request->reason]
            );
            $current->addDay();
        }

        return response()->json(['message' => 'Plage bloquée.']);
    }
}