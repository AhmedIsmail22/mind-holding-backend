<?php

namespace App\Http\Requests\Settings;

use App\DTOs\Settings\UpdateSettingsData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'array'],
            'company_name.ar' => ['required', 'string', 'max:255'],
            'company_name.en' => ['required', 'string', 'max:255'],

            'phone_landline' => ['nullable', 'string', 'max:50'],
            'phone_mobile_egypt' => ['nullable', 'string', 'max:50'],
            'whatsapp_egypt' => ['required', 'string', 'max:50'],
            'whatsapp_dubai' => ['required', 'string', 'max:50'],

            'gulf_countries' => ['required', 'array', 'min:1'],
            'gulf_countries.*' => ['string', 'size:2'],

            'email' => ['required', 'email', 'max:255'],
            'lead_notification_email' => ['required', 'email', 'max:255'],

            'address' => ['required', 'array'],
            'address.ar' => ['required', 'string', 'max:500'],
            'address.en' => ['required', 'string', 'max:500'],

            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['url', 'max:500'],

            'budget_options' => ['required', 'array', 'min:1'],
            'budget_options.*.ar' => ['required', 'string', 'max:255'],
            'budget_options.*.en' => ['required', 'string', 'max:255'],

            'start_timing_options' => ['required', 'array', 'min:1'],
            'start_timing_options.*.ar' => ['required', 'string', 'max:255'],
            'start_timing_options.*.en' => ['required', 'string', 'max:255'],

            'google_analytics_id' => ['nullable', 'string', 'max:50'],

            'logo' => ['nullable', 'image', 'max:2048'],
            'logo_alt' => ['nullable', 'array'],
            'logo_alt.ar' => ['nullable', 'string', 'max:255'],
            'logo_alt.en' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdateSettingsData
    {
        return UpdateSettingsData::fromArray($this->validated(), $this->file('logo'));
    }
}
