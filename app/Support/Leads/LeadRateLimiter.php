<?php

namespace App\Support\Leads;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Per-device lead submission limit. Deliberately separate from Laravel's
 * throttle middleware: that middleware hits the counter for every matching
 * request, including ones that go on to fail validation, which would
 * penalize a legitimate visitor for a typo. Here the check (did this device
 * already use its hour?) and the hit (count this one) are two different
 * calls, so only a request that actually results in a stored lead counts
 * (see LeadService::submit()); a 422 never does.
 */
final class LeadRateLimiter
{
    /** Matches the hour window Limit::perHour() used before this was split out. */
    private const DECAY_SECONDS = 3600;

    public static function deviceFor(Request $request): string
    {
        return $request->header('X-Device-Id') ?: $request->ip();
    }

    public static function tooManyAttempts(string $device): bool
    {
        return RateLimiter::tooManyAttempts(self::key($device), config('leads.submissions_per_hour'));
    }

    public static function hit(string $device): void
    {
        RateLimiter::hit(self::key($device), self::DECAY_SECONDS);
    }

    public static function availableIn(string $device): int
    {
        return RateLimiter::availableIn(self::key($device));
    }

    private static function key(string $device): string
    {
        return 'leads|'.$device;
    }
}
