<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->q, fn($q) => $q->where('name', 'like', "%{$request->q}%")->orWhere('email', 'like', "%{$request->q}%"))
            ->when($request->active !== null, fn($q) => $q->where('is_active', $request->boolean('active')))
            ->withCount(['reservations', 'villas'])
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['villas:id,title,status,owner_id', 'reservations' => fn($q) => $q->latest()->limit(5)]);
        return response()->json($user);
    }

    public function suspend(User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 422, 'Impossible de suspendre un admin.');
        $user->update(['is_active' => false]);
        $user->tokens()->delete();
        return response()->json(['message' => 'Compte suspendu.']);
    }

    public function activate(User $user): JsonResponse
    {
        $user->update(['is_active' => true]);
        return response()->json(['message' => 'Compte activé.']);
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if($user->isAdmin(), 422, 'Impossible de supprimer un admin.');
        $user->delete();
        return response()->json(['message' => 'Compte supprimé.']);
    }
}
