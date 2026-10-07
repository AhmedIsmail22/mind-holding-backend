<?php

namespace App\Http\Middleware;

use App\Support\Leads\LeadRateLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Rejects a lead request before it ever reaches validation if this device
 * already has five stored submissions this hour. It never increments the
 * counter itself - LeadService::submit() does that, and only once a lead is
 * actually stored - so a failed-validation or failed-reCAPTCHA request
 * never counts against the limit.
 */
class EnsureLeadRateLimitNotExceeded
{
    public function handle(Request $request, Closure $next): Response
    {
        $device = LeadRateLimiter::deviceFor($request);

        if (LeadRateLimiter::tooManyAttempts($device)) {
            throw new TooManyRequestsHttpException(LeadRateLimiter::availableIn($device));
        }

        return $next($request);
    }
}
