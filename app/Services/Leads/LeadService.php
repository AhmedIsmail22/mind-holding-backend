<?php

namespace App\Services\Leads;

use App\Contracts\RecaptchaVerifier;
use App\DTOs\Leads\SubmitLeadData;
use App\Models\Lead;
use App\Models\Setting;
use App\Support\Leads\MobileCountry;
use Illuminate\Validation\ValidationException;

class LeadService
{
    public function __construct(
        private readonly RecaptchaVerifier $recaptcha,
        private readonly LeadNotificationService $notifications,
    ) {}

    public function submit(SubmitLeadData $data, ?string $ip): Lead
    {
        // A filled honeypot means a bot. Store it as spam and answer as if
        // it worked, so the bot gets no signal to adapt to.
        if ($data->isHoneypotFilled) {
            return $this->store($data, 'spam');
        }

        if (! $this->recaptcha->verify($data->recaptchaToken, $data->type, $ip)) {
            throw ValidationException::withMessages([
                'recaptcha_token' => [__('validation.custom.recaptcha_token.spam_check_failed')],
            ]);
        }

        $lead = $this->store($data, 'new');

        $this->notifications->notifyNewLead($lead);

        return $lead;
    }

    private function store(SubmitLeadData $data, string $status): Lead
    {
        return Lead::create([
            'type' => $data->type,
            'status' => $status,
            'name' => $data->name,
            'mobile' => $data->mobile,
            'email' => $data->email,
            'company' => $data->company,
            'business_name' => $data->businessName,
            'service_id' => $data->serviceId,
            'solution_id' => $data->solutionId,
            'budget' => $data->budget,
            'start_timing' => $data->startTiming,
            'project_details' => $data->projectDetails,
            'preferred_contact_time' => $data->preferredContactTime,
            'need' => $data->need,
            'language' => $data->language,
            'page_url' => $data->pageUrl,
            'utm' => $data->utm,
        ]);
    }

    /**
     * Gulf-country prospects reach the Dubai number, everyone else the Egyptian
     * mobile (SRS §10, approved proposal).
     */
    public static function companyWhatsappFor(string $mobile): string
    {
        $setting = Setting::current();
        $isGulf = in_array(MobileCountry::iso($mobile), $setting->gulf_countries, true);

        return $isGulf ? $setting->whatsapp_dubai : $setting->whatsapp_egypt;
    }
}
