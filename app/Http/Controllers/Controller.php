<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class Controller
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }

    protected function paginated(LengthAwarePaginator $paginator, string $message = 'OK'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message);
    }

    protected function resourcePaginated(JsonResource|ResourceCollection $resource, string $message = 'OK'): JsonResponse
    {
        return ApiResponse::resourcePaginated($resource, $message);
    }

    protected function error(string $message = 'Error', array $errors = [], int $status = 400): JsonResponse
    {
        return ApiResponse::error($message, $errors, $status);
    }
}
