<?php

use App\Support\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;

it('builds a success envelope', function () {
    $response = ApiResponse::success(['foo' => 'bar'], 'Created', 201);

    expect($response->getStatusCode())->toBe(201);
    expect($response->getData(true))->toBe([
        'success' => true,
        'message' => 'Created',
        'data' => ['foo' => 'bar'],
    ]);
});

it('builds an error envelope and omits empty errors', function () {
    $response = ApiResponse::error('Nope', [], 400);

    expect($response->getData(true))->toBe([
        'success' => false,
        'message' => 'Nope',
    ]);
});

it('builds an error envelope with field errors', function () {
    $response = ApiResponse::error('Invalid', ['name' => ['The name field is required.']], 422);

    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true))->toBe([
        'success' => false,
        'message' => 'Invalid',
        'errors' => ['name' => ['The name field is required.']],
    ]);
});

it('builds a paginated envelope with meta', function () {
    $paginator = new LengthAwarePaginator(['a', 'b'], 20, 2, 1);

    $response = ApiResponse::paginated($paginator, 'OK');
    $body = $response->getData(true);

    expect($body['success'])->toBeTrue();
    expect($body['data'])->toBe(['a', 'b']);
    expect($body['meta'])->toBe([
        'current_page' => 1,
        'last_page' => 10,
        'per_page' => 2,
        'total' => 20,
    ]);
});
