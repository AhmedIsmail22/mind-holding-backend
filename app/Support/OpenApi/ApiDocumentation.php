<?php

namespace App\Support\OpenApi;

use Dedoc\Scramble\Contracts\OperationTransformer;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;

/**
 * Adds what Scramble cannot infer from the code: the Accept-Language header on
 * public routes, bearer auth on admin routes, and the error envelopes that
 * the exception handler produces (see bootstrap/app.php).
 */
final class ApiDocumentation implements OperationTransformer
{
    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        $uri = $routeInfo->route->uri();
        $middleware = $routeInfo->route->gatherMiddleware();

        if (str_starts_with($uri, 'api/v1/public/')) {
            $operation->parameters[] = self::acceptLanguage();
        }

        $requiresAuth = in_array('auth:sanctum', $middleware, true);

        if ($requiresAuth) {
            $operation->addSecurity(new SecurityRequirement(['bearerAuth' => []]));
            $operation->addResponse(self::error(401, 'Unauthenticated.'));
        }

        if (collect($middleware)->contains(fn ($m) => str_starts_with((string) $m, 'permission:'))) {
            $operation->addResponse(self::error(403, 'This action is unauthorized.'));
        }

        if (in_array('throttle:leads', $middleware, true)) {
            $operation->addResponse(self::error(429, 'Too many requests. Please try again later.'));
        }

        if (str_contains($uri, '{')) {
            $operation->addResponse(self::error(404, 'Resource not found.'));
        }

        $isLogout = str_ends_with($uri, 'auth/logout');
        if (in_array($operation->method, ['post', 'put', 'patch'], true) && ! $isLogout) {
            $operation->addResponse(self::validationError());
        }
    }

    private static function acceptLanguage(): Parameter
    {
        $parameter = Parameter::make('Accept-Language', 'header');
        $parameter->description = 'Response language. "ar" is the default; "en" is the only other supported value.';

        $schema = new Schema;
        $schema->type = new StringType;
        $schema->default = 'ar';
        $schema->enum = ['ar', 'en'];
        $parameter->schema = $schema;

        return $parameter;
    }

    private static function error(int $code, string $message): Response
    {
        return self::response($code, 'Error', [
            'type' => 'object',
            'properties' => [
                'success' => ['type' => 'boolean', 'const' => false],
                'message' => ['type' => 'string', 'example' => $message],
            ],
            'required' => ['success', 'message'],
        ]);
    }

    private static function validationError(): Response
    {
        return self::response(422, 'Validation failed', [
            'type' => 'object',
            'properties' => [
                'success' => ['type' => 'boolean', 'const' => false],
                'message' => ['type' => 'string', 'example' => 'The given data was invalid.'],
                'errors' => [
                    'type' => 'object',
                    'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']],
                ],
            ],
            'required' => ['success', 'message'],
        ]);
    }

    private static function response(int $code, string $description, array $schema): Response
    {
        $response = Response::make($code)->setDescription($description);
        $response->setContent('application/json', new class($schema)
        {
            public function __construct(private readonly array $schema) {}

            public function toArray(): array
            {
                return $this->schema;
            }
        });

        return $response;
    }
}
