<?php

namespace App\DTOs\Leads;

final readonly class SubmitLeadData
{
    public function __construct(
        public string $type,
        public string $name,
        public string $mobile,
        public ?string $email,
        public ?string $company,
        public ?string $businessName,
        public ?int $serviceId,
        public ?int $solutionId,
        public ?string $budget,
        public ?string $startTiming,
        public ?string $projectDetails,
        public ?string $preferredContactTime,
        public ?string $need,
        public string $pageUrl,
        public ?array $utm,
        public bool $isHoneypotFilled,
        public string $recaptchaToken,
        public string $language,
        public ?string $deviceId,
    ) {}
}
