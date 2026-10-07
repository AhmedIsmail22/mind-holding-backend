<?php

namespace App\DTOs\HomeContent;

use Illuminate\Http\UploadedFile;

final readonly class UpdateHomeContentData
{
    public function __construct(
        public array $heroHeadline,
        public array $heroSubheadline,
        public array $stats,
        public array $differentiators,
        public array $processSteps,
        public array $closingCtaHeadline,
        public ?array $closingCtaSubheadline,
        public ?UploadedFile $heroImage,
        public ?array $heroImageAlt,
    ) {}

    public static function fromArray(array $data, ?UploadedFile $heroImage): self
    {
        return new self(
            heroHeadline: $data['hero_headline'],
            heroSubheadline: $data['hero_subheadline'],
            stats: $data['stats'] ?? [],
            differentiators: $data['differentiators'],
            processSteps: $data['process_steps'],
            closingCtaHeadline: $data['closing_cta_headline'],
            closingCtaSubheadline: $data['closing_cta_subheadline'] ?? null,
            heroImage: $heroImage,
            heroImageAlt: $data['hero_image_alt'] ?? null,
        );
    }
}
