<?php

namespace App\Http\Requests\HomeContent;

use App\DTOs\HomeContent\UpdateHomeContentData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hero_headline' => ['required', 'array'],
            'hero_headline.ar' => ['required', 'string', 'max:255'],
            'hero_headline.en' => ['required', 'string', 'max:255'],

            'hero_subheadline' => ['required', 'array'],
            'hero_subheadline.ar' => ['required', 'string', 'max:500'],
            'hero_subheadline.en' => ['required', 'string', 'max:500'],

            'stats' => ['nullable', 'array', 'max:4'],
            'stats.*.label.ar' => ['required', 'string', 'max:100'],
            'stats.*.label.en' => ['required', 'string', 'max:100'],
            'stats.*.value' => ['required', 'string', 'max:50'],

            'differentiators' => ['required', 'array', 'min:1', 'max:6'],
            'differentiators.*.title.ar' => ['required', 'string', 'max:255'],
            'differentiators.*.title.en' => ['required', 'string', 'max:255'],
            'differentiators.*.description.ar' => ['required', 'string', 'max:500'],
            'differentiators.*.description.en' => ['required', 'string', 'max:500'],

            'process_steps' => ['required', 'array', 'min:1'],
            'process_steps.*.title.ar' => ['required', 'string', 'max:255'],
            'process_steps.*.title.en' => ['required', 'string', 'max:255'],
            'process_steps.*.description.ar' => ['required', 'string', 'max:500'],
            'process_steps.*.description.en' => ['required', 'string', 'max:500'],
            'process_steps.*.duration.ar' => ['required', 'string', 'max:100'],
            'process_steps.*.duration.en' => ['required', 'string', 'max:100'],

            'closing_cta_headline' => ['required', 'array'],
            'closing_cta_headline.ar' => ['required', 'string', 'max:255'],
            'closing_cta_headline.en' => ['required', 'string', 'max:255'],

            'closing_cta_subheadline' => ['nullable', 'array'],
            'closing_cta_subheadline.ar' => ['nullable', 'string', 'max:500'],
            'closing_cta_subheadline.en' => ['nullable', 'string', 'max:500'],

            'hero_image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function toDto(): UpdateHomeContentData
    {
        return UpdateHomeContentData::fromArray($this->validated(), $this->file('hero_image'));
    }
}
