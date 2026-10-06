<?php

namespace App\Providers;

use App\Contracts\RecaptchaVerifier;
use App\Support\Leads\GoogleRecaptchaVerifier;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecaptchaVerifier::class, GoogleRecaptchaVerifier::class);
    }

    public function boot(): void
    {
        // Per device: the X-Device-Id header when the frontend sends one,
        // otherwise the client IP. The header is client-supplied, so this is
        // a soft limit, not a hard guarantee.
        RateLimiter::for('leads', function ($request) {
            $device = $request->header('X-Device-Id') ?: $request->ip();

            return Limit::perHour(config('leads.submissions_per_hour'))->by('leads|'.$device);
        });
    }
}
