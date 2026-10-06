<?php

namespace App\Http\Requests\Leads;

use App\DTOs\Leads\SubmitLeadData;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseLeadRequest extends FormRequest
{
    abstract protected function leadType(): string;

    /** @return array<string, mixed> */
    abstract protected function typeRules(): array;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->mobile)) {
            $this->merge(['mobile' => preg_replace('/[\s\-()]/', '', $this->mobile)]);
        }
    }

    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'regex:/^\+(20\d{10}|966\d{8,9}|971\d{8,9}|965\d{8}|974\d{8}|973\d{8}|968\d{8})$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'page_url' => ['required', 'url', 'max:500'],
            'utm' => ['nullable', 'array'],
            'utm.source' => ['nullable', 'string', 'max:255'],
            'utm.medium' => ['nullable', 'string', 'max:255'],
            'utm.campaign' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string'],
            'recaptcha_token' => ['required', 'string'],
        ], $this->typeRules());
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid Egyptian or Gulf mobile number with its country code.',
        ];
    }

    public function toDto(): SubmitLeadData
    {
        $data = $this->validated();

        return new SubmitLeadData(
            type: $this->leadType(),
            name: $data['name'],
            mobile: $data['mobile'],
            email: $data['email'] ?? null,
            company: $data['company'] ?? null,
            businessName: $data['business_name'] ?? null,
            serviceId: isset($data['service_id']) ? (int) $data['service_id'] : null,
            solutionId: isset($data['solution_id']) ? (int) $data['solution_id'] : null,
            budget: $data['budget'] ?? null,
            startTiming: $data['start_timing'] ?? null,
            projectDetails: $data['project_details'] ?? null,
            preferredContactTime: $data['preferred_contact_time'] ?? null,
            need: $data['need'] ?? null,
            pageUrl: $data['page_url'],
            utm: $data['utm'] ?? null,
            isHoneypotFilled: filled($data['website'] ?? null),
            recaptchaToken: $data['recaptcha_token'],
            language: app()->getLocale(),
        );
    }

    /**
     * Budget and start-timing values are admin-managed lists in Settings.
     * Accept either locale's label so the frontend can send whichever it shows.
     */
    protected function optionLabels(string $field): array
    {
        $options = Setting::current()->{$field} ?? [];

        return collect($options)
            ->flatMap(fn ($option) => [$option['ar'] ?? null, $option['en'] ?? null])
            ->filter()
            ->values()
            ->all();
    }
}
