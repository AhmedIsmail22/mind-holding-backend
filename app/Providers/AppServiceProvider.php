<?php

namespace App\Providers;

use App\Contracts\RecaptchaVerifier;
use App\Support\Leads\GoogleRecaptchaVerifier;
use App\Support\OpenApi\ApiDocumentation;
use Dedoc\Scramble\Configuration\OperationTransformers;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Routing\Route;
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

        Scramble::configure()
            ->routes(fn (Route $route) => str_starts_with($route->uri(), 'api/') && ! str_contains($route->uri(), '_probe'))
            ->withOperationTransformers(fn (OperationTransformers $transformers) => $transformers->append(ApiDocumentation::class))
            ->withDocumentTransformers(function (OpenApi $document) {
                $document->components->addSecurityScheme('bearerAuth', SecurityScheme::http('bearer'));
            });
    }
}
