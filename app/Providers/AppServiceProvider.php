<?php

namespace App\Providers;

use App\Contracts\RecaptchaVerifier;
use App\Support\Leads\GoogleRecaptchaVerifier;
use App\Support\OpenApi\ApiDocumentation;
use Dedoc\Scramble\Configuration\OperationTransformers;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecaptchaVerifier::class, GoogleRecaptchaVerifier::class);
    }

    public function boot(): void
    {
        Scramble::configure()
            ->routes(fn (Route $route) => str_starts_with($route->uri(), 'api/') && ! str_contains($route->uri(), '_probe'))
            ->withOperationTransformers(fn (OperationTransformers $transformers) => $transformers->append(ApiDocumentation::class))
            ->withDocumentTransformers(function (OpenApi $document) {
                $document->components->addSecurityScheme('bearerAuth', SecurityScheme::http('bearer'));
            });
    }
}
