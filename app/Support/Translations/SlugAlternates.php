<?php

namespace App\Support\Translations;

use Illuminate\Database\Eloquent\Model;

/**
 * Effective slug per locale, for hreflang and the language switcher. Arabic
 * uses slug_ar when it is set and otherwise falls back to the English slug,
 * so a published record always has an Arabic alternate.
 */
final class SlugAlternates
{
    /** @return array{ar: ?string, en: ?string} */
    public static function for(Model $model): array
    {
        return [
            'ar' => self::effective($model->slug_ar, $model->slug),
            'en' => self::effective($model->slug, null),
        ];
    }

    public static function effective(?string $preferred, ?string $fallback): ?string
    {
        if (filled($preferred)) {
            return $preferred;
        }

        return filled($fallback) ? $fallback : null;
    }
}
