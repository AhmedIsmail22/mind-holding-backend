<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Admin\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->toDto(), $request->ip());

        return $this->success([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ], 'Logged in.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()->load('roles')));
    }
}
