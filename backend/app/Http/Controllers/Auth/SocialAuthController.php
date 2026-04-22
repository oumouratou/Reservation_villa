<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    protected array $providers = ['google', 'facebook'];

    public function redirect(string $provider): JsonResponse
    {
        abort_unless(in_array($provider, $this->providers), 404);

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function callback(string $provider): JsonResponse
    {
        abort_unless(in_array($provider, $this->providers), 404);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Authentification OAuth échouée.'], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'        => $socialUser->getName(),
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar'      => $socialUser->getAvatar(),
                'role'        => 'traveler',
                'password'    => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        if ($user->exists && !$user->provider) {
            $user->update(['provider' => $provider, 'provider_id' => $socialUser->getId()]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }
}