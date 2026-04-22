<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'travelers' => User::where('role', 'traveler')->count(),
                'owners' => User::where('role', 'owner')->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
            ],
            'villas' => [
                'total' => Villa::count(),
                'approved' => Villa::where('status', 'approved')->count(),
                'pending' => Villa::where('status', 'pending')->count(),
            ],
            'reservations' => [
                'total' => Reservation::count(),
                'pending' => Reservation::where('status', 'pending')->count(),
                'completed' => Reservation::where('status', 'completed')->count(),
                'this_month' => Reservation::whereMonth('created_at', now()->month)->count(),
            ],
            'revenue' => [
                'total' => Payment::where('status', 'completed')->sum('amount'),
                'this_month' => Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            ],
            'disputes' => [
                'open' => Dispute::where('status', 'open')->count(),
                'total' => Dispute::count(),
            ],
        ];

        $recentReservations = Reservation::with(['villa:id,title', 'traveler:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        $monthlyRevenue = Payment::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json(compact('stats', 'recentReservations', 'monthlyRevenue'));
    }
}
