<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Redirects\RedirectRequest;
use App\Http\Resources\Admin\RedirectResource;
use App\Models\Redirect;
use App\Services\Redirects\RedirectService;
use Illuminate\Http\JsonResponse;

class RedirectController extends Controller
{
    public function __construct(
        private readonly RedirectService $redirectService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(RedirectResource::collection($this->redirectService->list()));
    }

    public function store(RedirectRequest $request): JsonResponse
    {
        return $this->success(new RedirectResource($this->redirectService->create($request->toDto())), 'Redirect created.', 201);
    }

    public function show(Redirect $redirect): JsonResponse
    {
        return $this->success(new RedirectResource($redirect));
    }

    public function update(RedirectRequest $request, Redirect $redirect): JsonResponse
    {
        return $this->success(new RedirectResource($this->redirectService->update($redirect, $request->toDto())), 'Redirect updated.');
    }

    public function destroy(Redirect $redirect): JsonResponse
    {
        $this->redirectService->delete($redirect);

        return $this->success(null, 'Redirect deleted.');
    }
}
