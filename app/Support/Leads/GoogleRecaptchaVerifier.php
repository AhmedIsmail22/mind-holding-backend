<?php

namespace App\Support\Leads;

use App\Contracts\RecaptchaVerifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleRecaptchaVerifier implements RecaptchaVerifier
{
    /**
     * Sentinel the frontend can send before real reCAPTCHA keys are wired
     * up on its side. Recognized only in local/testing (see
     * isLocalOrTesting()) - rejected everywhere else even if a secret
     * happens to be configured, so it can never become a production
     * backdoor.
     */
    public const PLACEHOLDER_TOKEN = 'test-token';

    public function verify(string $token, string $action, ?string $ip): bool
    {
        if ($token === self::PLACEHOLDER_TOKEN) {
            return $this->isLocalOrTesting();
        }

        $secret = config('services.recaptcha.secret');

        if (empty($secret)) {
            if ($this->isLocalOrTesting()) {
                Log::info('reCAPTCHA secret is not configured; allowing because APP_ENV is '.app()->environment().'.');

                return true;
            }

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

    private function isLocalOrTesting(): bool
    {
        return app()->environment(['local', 'testing']);
    }
}
