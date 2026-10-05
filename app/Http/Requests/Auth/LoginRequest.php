<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\LoginData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(): LoginData
    {
        return LoginData::fromArray($this->validated());
    }
}
