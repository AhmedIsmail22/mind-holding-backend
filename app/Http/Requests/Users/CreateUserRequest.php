<?php

namespace App\Http\Requests\Users;

use App\DTOs\Users\CreateUserData;
use App\Support\Auth\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(Roles::ALL)],
        ];
    }

    public function toDto(): CreateUserData
    {
        return CreateUserData::fromArray($this->validated());
    }
}
