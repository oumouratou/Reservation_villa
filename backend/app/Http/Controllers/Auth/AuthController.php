<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
            'role'     => 'required|in:client,traveler,owner',
        ]);

        try {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Compte créé avec succès.',
                'user'    => $this->formatUser($user),
                'token'   => $token,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création du compte.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Compte suspendu. Contactez l\'administration.'], 403);
        }

        // Revoke old tokens
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $this->formatUser($user),
            'token'   => $token,
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($this->formatUser($request->user()));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name'  => 'sometimes|string|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
            'bio'   => 'sometimes|nullable|string|max:500',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user'    => $this->formatUser($user),
        ]);
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Lien de réinitialisation envoyé.'])
            : response()->json(['message' => 'Email non trouvé.'], 422);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mot de passe réinitialisé.'])
            : response()->json(['message' => 'Token invalide ou expiré.'], 422);
    }

    private function formatUser(User $user): array
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'role'              => $user->role,
            'phone'             => $user->phone,
            'avatar'            => $user->avatar,
            'bio'               => $user->bio,
            'email_verified_at' => $user->email_verified_at,
            'is_active'         => $user->is_active,
            'created_at'        => $user->created_at,
        ];
    }
}