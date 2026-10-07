<?php

namespace App\Support\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Image handling shared by every model that serves pictures. Dimensions of
 * each served conversion are recorded once at upload, so public responses
 * never read image files. Alt text is stored per media as a translatable
 * array in the "alt" custom property.
 */
final class MediaAsset
{
    private const CONVERSIONS = ['webp', 'thumb', 'og'];

    /** Adds an upload to a collection, with optional alt text, and records conversion sizes. */
    public static function attach(Model $owner, UploadedFile $file, string $collection, ?array $alt = null): Media
    {
        $media = $owner->addMedia($file)
            ->withCustomProperties(['alt' => $alt])
            ->toMediaCollection($collection);

        self::recordDimensions($media);

        return $media;
    }

    /** Replaces the collection's image (used by singleton owners such as the hero or the logo). */
    public static function replace(Model $owner, UploadedFile $file, string $collection, ?array $alt = null): Media
    {
        $owner->clearMediaCollection($collection);

        return self::attach($owner, $file, $collection, $alt);
    }

    /** Updates alt text on an existing image without re-uploading it. */
    public static function setAlt(?Media $media, ?array $alt): void
    {
        if ($media === null || $alt === null) {
            return;
        }

        $media->setCustomProperty('alt', $alt);
        $media->save();
    }

    /**
     * Public shape for one served conversion. Null when there is no image.
     *
     * @return array{url: string, width: ?int, height: ?int, alt: ?string}|null
     */
    public static function present(?Media $media, string $conversion = 'webp'): ?array
    {
        if ($media === null) {
            return null;
        }

        $served = $media->hasGeneratedConversion($conversion) ? $conversion : null;
        [$width, $height] = self::sizeFor($media, $served);

        return [
            'url' => $served ? $media->getUrl($served) : $media->getUrl(),
            'width' => $width,
            'height' => $height,
            'alt' => self::localizedAlt($media),
        ];
    }

    /** Both locales for admin editing, or null when no alt is set. */
    public static function altBothLocales(?Media $media): ?array
    {
        $alt = $media?->getCustomProperty('alt');

        if (! is_array($alt)) {
            return null;
        }

        $both = ['ar' => $alt['ar'] ?? null, 'en' => $alt['en'] ?? null];

        return $both['ar'] === null && $both['en'] === null ? null : $both;
    }

    private static function localizedAlt(Media $media): ?string
    {
        $alt = $media->getCustomProperty('alt');

        if (! is_array($alt)) {
            return null;
        }

        $locale = app()->getLocale();
        $value = $alt[$locale] ?? null;

        if (is_string($value) && $value !== '') {
            return $value;
        }

        $fallback = $alt['en'] ?? null;

        return is_string($fallback) && $fallback !== '' ? $fallback : null;
    }

    public static function recordDimensions(Media $media): void
    {
        $dimensions = [];
        $original = @getimagesize($media->getPath());

        if ($original !== false) {
            $dimensions['original'] = ['width' => $original[0], 'height' => $original[1]];
        }

        foreach (self::CONVERSIONS as $conversion) {
            if (! $media->hasGeneratedConversion($conversion)) {
                continue;
            }

            $size = @getimagesize($media->getPath($conversion));

            if ($size !== false) {
                $dimensions[$conversion] = ['width' => $size[0], 'height' => $size[1]];
            }
        }

        $media->setCustomProperty('dimensions', $dimensions);
        $media->save();
    }

    /** @return array{0: ?int, 1: ?int} */
    private static function sizeFor(Media $media, ?string $conversion): array
    {
        $dimensions = $media->getCustomProperty('dimensions', []);
        $key = $conversion ?? 'original';
        $size = $dimensions[$key] ?? null;

        return $size === null ? [null, null] : [(int) $size['width'], (int) $size['height']];
    }
}
