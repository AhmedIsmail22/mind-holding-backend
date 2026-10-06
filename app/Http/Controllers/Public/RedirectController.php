<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\RedirectResource;
use App\Services\Redirects\RedirectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __construct(
        private readonly RedirectService $redirectService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(RedirectResource::collection($this->redirectService->list()));
    }

    public function resolve(Request $request): JsonResponse
    {
        $request->validate(['path' => ['required', 'string', 'max:500']]);

        $redirect = $this->redirectService->resolve($request->string('path')->toString());

        if ($redirect === null) {
            return $this->error('No redirect for this path.', [], 404);
        }

        return $this->success(new RedirectResource($redirect));
    }
}
