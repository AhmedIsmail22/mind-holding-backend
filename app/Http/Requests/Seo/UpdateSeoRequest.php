<?php

namespace App\Http\Requests\Seo;

use App\DTOs\Seo\UpdateSeoData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:70'],
            'title.en' => ['required', 'string', 'max:70'],

            'description' => ['required', 'array'],
            'description.ar' => ['required', 'string', 'max:160'],
            'description.en' => ['required', 'string', 'max:160'],

            'share_image' => ['nullable', 'image', 'max:4096'],
            'share_image_alt' => ['nullable', 'array'],
            'share_image_alt.ar' => ['nullable', 'string', 'max:255'],
            'share_image_alt.en' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdateSeoData
    {
        return UpdateSeoData::fromArray($this->validated(), $this->file('share_image'));
    }
}
