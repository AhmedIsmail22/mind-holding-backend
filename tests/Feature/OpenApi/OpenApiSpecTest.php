<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

function committedSpec(): array
{
    return json_decode(file_get_contents(base_path('docs/openapi.json')), true, flags: JSON_THROW_ON_ERROR);
}

function generatedSpec(): array
{
    $path = storage_path('framework/testing/openapi-'.uniqid().'.json');
    @mkdir(dirname($path), 0777, true);

    Artisan::call('scramble:export', ['--path' => $path]);

    return json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
}

it('matches the committed docs/openapi.json so the spec is never stale', function () {
    $generated = generatedSpec();

    expect($generated)->toEqual(committedSpec(),
        'docs/openapi.json is stale. Regenerate it with: php artisan scramble:export --path=docs/openapi.json'
    );
});

it('documents every public route with the HTTP methods it accepts', function () {
    $spec = committedSpec();

    $publicRoutes = collect(Route::getRoutes())
        ->filter(fn ($route) => str_starts_with($route->uri(), 'api/v1/public/'));

    expect($publicRoutes)->not->toBeEmpty();

    foreach ($publicRoutes as $route) {
        $path = '/'.substr($route->uri(), strlen('api/'));

        foreach (array_diff($route->methods(), ['HEAD']) as $method) {
            expect($spec['paths'])->toHaveKey($path);
            expect($spec['paths'][$path])->toHaveKey(strtolower($method));
        }
    }
});

it('documents the success envelope on every operation that returns data', function () {
    $spec = committedSpec();

    foreach ($spec['paths'] as $path => $methods) {
        foreach ($methods as $method => $operation) {
            $success = $operation['responses']['200'] ?? $operation['responses']['201'] ?? null;

            if ($success === null) {
                continue;
            }

            $schema = $success['content']['application/json']['schema'] ?? null;

            expect($schema)->not->toBeNull("{$method} {$path} has no JSON schema");
            expect($schema['properties'])->toHaveKeys(['success', 'message', 'data'], "{$method} {$path}");
        }
    }
});

it('documents the public Accept-Language header and bearer auth on admin routes', function () {
    $spec = committedSpec();

    $publicHeaders = collect($spec['paths']['/v1/public/faqs']['get']['parameters'] ?? [])->pluck('name');
    expect($publicHeaders)->toContain('Accept-Language');

    expect($spec['paths']['/v1/admin/faqs']['post']['security'] ?? null)->toBe([['bearerAuth' => []]]);
    expect($spec['components']['securitySchemes']['bearerAuth'] ?? null)->toBe(['type' => 'http', 'scheme' => 'bearer']);
});
