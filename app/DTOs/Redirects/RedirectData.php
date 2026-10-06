<?php

namespace App\DTOs\Redirects;

final readonly class RedirectData
{
    public function __construct(
        public string $oldPath,
        public string $newPath,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            oldPath: self::normalize($data['old_path']),
            newPath: self::normalize($data['new_path']),
        );
    }

    public static function normalize(string $path): string
    {
        $path = '/'.ltrim(trim($path), '/');

        return $path === '/' ? $path : rtrim($path, '/');
    }
}
