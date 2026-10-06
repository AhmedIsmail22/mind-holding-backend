<?php

namespace App\Http\Requests\Technologies;

use App\DTOs\Technologies\CreateTechnologyData;
use Illuminate\Foundation\Http\FormRequest;

class CreateTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'array'],
            'category.ar' => ['required', 'string', 'max:100'],
            'category.en' => ['required', 'string', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:1024'],
        ];
    }

    public function toDto(): CreateTechnologyData
    {
        return CreateTechnologyData::fromArray($this->validated(), $this->file('logo'));
    }
}
