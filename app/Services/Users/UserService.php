<?php

namespace App\Services\Users;

use App\DTOs\Users\CreateUserData;
use App\DTOs\Users\UpdateUserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')->orderBy('name')->paginate($perPage);
    }

    public function create(CreateUserData $data): User
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $user->syncRoles([$data->role]);

        return $user;
    }

    public function update(User $user, UpdateUserData $data, User $actingUser): User
    {
        $user->name = $data->name;
        $user->email = $data->email;

        if ($data->password !== null) {
            $user->password = Hash::make($data->password);
        }

        $user->save();
        $user->syncRoles([$data->role]);

        return $user;
    }

    public function delete(User $user, User $actingUser): void
    {
        if ($user->is($actingUser)) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own account.'],
            ]);
        }

        $user->delete();
    }
}
