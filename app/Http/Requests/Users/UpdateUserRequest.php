<?php

namespace App\Http\Requests\Users;

use App\DTOs\Users\UpdateUserData;
use App\Support\Auth\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(Roles::ALL)],
        ];
    }

    public function toDto(): UpdateUserData
    {
        return UpdateUserData::fromArray($this->validated());
    }
}
