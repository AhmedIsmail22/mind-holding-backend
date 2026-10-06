<?php

namespace App\DTOs\Technologies;

use Illuminate\Http\UploadedFile;

final readonly class UpdateTechnologyData
{
    public function __construct(
        public string $name,
        public array $category,
        public int $order,
        public ?UploadedFile $logo,
    ) {}

    public static function fromArray(array $data, ?UploadedFile $logo): self
    {
        return new self(
            name: $data['name'],
            category: $data['category'],
            order: $data['order'] ?? 0,
            logo: $logo,
        );
    }
}
