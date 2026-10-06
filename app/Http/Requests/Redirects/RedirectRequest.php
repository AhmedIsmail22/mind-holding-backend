<?php

namespace App\Http\Requests\Redirects;

use App\DTOs\Redirects\RedirectData;
use App\Models\Redirect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignore = $this->route('redirect');

        return [
            'old_path' => [
                'required',
                'string',
                'max:500',
                'regex:/^\/[^\s?#]*$/',
                // Active rows only: a soft-deleted redirect's path may be reused.
                Rule::unique(Redirect::class, 'old_path')->whereNull('deleted_at')->ignore($ignore),
            ],
            'new_path' => ['required', 'string', 'max:500', 'regex:/^\/[^\s?#]*$/', 'different:old_path'],
        ];
    }

    public function toDto(): RedirectData
    {
        return RedirectData::fromArray($this->validated());
    }
}
