<?php

namespace App\DTOs\Seo;

use Illuminate\Http\UploadedFile;

final readonly class UpdateSeoData
{
    public function __construct(
        public array $title,
        public array $description,
        public ?UploadedFile $shareImage,
    ) {}

    public static function fromArray(array $data, ?UploadedFile $shareImage): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            shareImage: $shareImage,
        );
    }
}
