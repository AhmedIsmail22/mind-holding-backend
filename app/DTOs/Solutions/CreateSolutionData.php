<?php

namespace App\DTOs\Solutions;

final readonly class CreateSolutionData
{
    public function __construct(
        public int $solutionIndustryId,
        public array $name,
        public string $slug,
        public array $audience,
        public ?array $summary,
        public array $problemPoints,
        public array $features,
        public array $deliverables,
        public ?string $demoUrl,
        public ?string $demoCredentials,
        public bool $isFlagship,
        public bool $isPublished,
        public int $order,
        public array $relatedSolutionIds,
        public array $relatedServiceIds,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            solutionIndustryId: (int) $data['solution_industry_id'],
            name: $data['name'],
            slug: $data['slug'],
            audience: $data['audience'],
            summary: $data['summary'] ?? null,
            problemPoints: $data['problem_points'],
            features: [
                'customer' => $data['features']['customer'] ?? [],
                'business_owner' => $data['features']['business_owner'] ?? [],
                'staff' => $data['features']['staff'] ?? [],
            ],
            deliverables: $data['deliverables'],
            demoUrl: $data['demo_url'] ?? null,
            demoCredentials: $data['demo_credentials'] ?? null,
            isFlagship: $data['is_flagship'] ?? false,
            isPublished: $data['is_published'] ?? false,
            order: $data['order'] ?? 0,
            relatedSolutionIds: $data['related_solution_ids'] ?? [],
            relatedServiceIds: $data['related_service_ids'] ?? [],
        );
    }
}
