<?php

namespace App\DTOs\Projects;

final readonly class CreateProjectData
{
    public function __construct(
        public string $slug,
        public ?string $slugAr,
        public array $clientName,
        public ?array $summary,
        public bool $hideClientName,
        public ?array $genericDescription,
        public array $overview,
        public array $challenge,
        public array $solution,
        public array $technologies,
        public ?string $liveUrl,
        public bool $isPublished,
        public int $order,
        public array $relatedServiceIds,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'],
            slugAr: $data['slug_ar'] ?? null,
            clientName: $data['client_name'],
            summary: $data['summary'] ?? null,
            hideClientName: $data['hide_client_name'] ?? false,
            genericDescription: $data['generic_description'] ?? null,
            overview: $data['overview'],
            challenge: $data['challenge'],
            solution: $data['solution'],
            technologies: $data['technologies'],
            liveUrl: $data['live_url'] ?? null,
            isPublished: $data['is_published'] ?? false,
            order: $data['order'] ?? 0,
            relatedServiceIds: $data['related_service_ids'] ?? [],
        );
    }
}
