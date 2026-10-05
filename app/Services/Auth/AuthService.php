<?php

namespace App\Services\Auth;

use App\DTOs\Auth\LoginData;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    public function __construct(
        private readonly RateLimiter $limiter,
    ) {}

    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginData $data, string $ip): array
    {
        $key = $this->throttleKey($data->email, $ip);

        if ($this->limiter->tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = $this->limiter->availableIn($key);

            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $user = User::where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            $this->limiter->hit($key, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $this->limiter->clear($key);

        $token = $user->createToken('admin-dashboard')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    private function throttleKey(string $email, string $ip): string
    {
        return Str::transliterate(Str::lower($email)).'|'.$ip;
    }
}
