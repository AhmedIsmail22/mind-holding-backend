<?php

namespace App\Services\Leads;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Models\Setting;
use App\Support\Leads\MobileCountry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LeadNotificationService
{
    /**
     * The lead is already saved by the time this runs, so a mail failure is
     * logged and never fails the visitor's request.
     */
    public function notifyNewLead(Lead $lead): void
    {
        try {
            Mail::to(Setting::current()->lead_notification_email)->send(new NewLeadMail(
                subjectLine: "New {$lead->type} request from {$lead->name}",
                body: $this->body($lead),
            ));
        } catch (Throwable $e) {
            Log::error('Lead notification failed.', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }

    private function body(Lead $lead): string
    {
        $lines = [
            'Type: '.$lead->type,
            'Name: '.$lead->name,
            'Mobile: '.$lead->mobile,
            'Email: '.($lead->email ?? '-'),
            'Page: '.$lead->page_url,
            'Language: '.$lead->language,
            'WhatsApp the prospect: https://wa.me/'.MobileCountry::digits($lead->mobile),
            'Dashboard ID: '.$lead->id,
        ];

        foreach (['company', 'business_name', 'service', 'solution', 'budget', 'start_timing', 'preferred_contact_time', 'need', 'project_details'] as $field) {
            $value = match ($field) {
                'service' => $lead->service?->getTranslation('name', 'en'),
                'solution' => $lead->solution?->getTranslation('name', 'en'),
                default => $lead->{$field},
            };

            if ($value !== null && $value !== '') {
                $lines[] = ucfirst(str_replace('_', ' ', $field)).': '.$value;
            }
        }

        return implode("\n", $lines);
    }
}
