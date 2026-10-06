<?php

namespace App\Support\Leads;

use App\Contracts\RecaptchaVerifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleRecaptchaVerifier implements RecaptchaVerifier
{
    public function verify(string $token, string $action, ?string $ip): bool
    {
        $secret = config('services.recaptcha.secret');

        if (empty($secret)) {
            // Fail closed: a missing key must never let submissions through.
            Log::warning('reCAPTCHA secret is not configured; rejecting submission.');

            return false;
        }

        $response = Http::asForm()->timeout(5)->post(config('services.recaptcha.url'), [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if (! $response->successful()) {
            return false;
        }

        $body = $response->json();

        return ($body['success'] ?? false) === true
            && ($body['action'] ?? null) === $action
            && ($body['score'] ?? 0) >= config('services.recaptcha.score_threshold');
    }
}
