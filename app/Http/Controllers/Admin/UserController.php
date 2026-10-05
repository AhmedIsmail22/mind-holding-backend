<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->list();

        return $this->resourcePaginated(UserResource::collection($users));
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->toDto());

        return $this->success(new UserResource($user), 'User created.', 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user->load('roles')));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->toDto(), $request->user());

        return $this->success(new UserResource($user), 'User updated.');
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->userService->delete($user, $request->user());

        return $this->success(null, 'User deleted.');
    }
}
