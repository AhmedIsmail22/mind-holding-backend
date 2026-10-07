<?php

namespace App\Support\Translations;

use Illuminate\Database\Eloquent\Model;

/**
 * Localized slug alternates for hreflang and the language switcher. A locale
 * is null when that locale has no slug, never a fallback to the other one.
 */
final class SlugAlternates
{
    /** @return array{ar: ?string, en: ?string} */
    public static function for(Model $model): array
    {
        return [
            'ar' => self::filled($model->slug_ar) ? $model->slug_ar : null,
            'en' => self::filled($model->slug) ? $model->slug : null,
        ];
    }

    private static function filled(?string $value): bool
    {
        return $value !== null && $value !== '';
    }
}
