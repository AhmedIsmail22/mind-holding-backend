<?php

namespace App\Support\Translations;

use Illuminate\Database\Eloquent\Model;

/**
 * Reads an optional translatable attribute. Spatie stores a null value as
 * {"en": null} rather than SQL NULL, and its accessor then returns '', so
 * these helpers return null whenever no locale holds a value.
 */
final class OptionalTranslation
{
    /** Current locale, falling back to English. Null when nothing is set. */
    public static function current(Model $model, string $attribute): ?string
    {
        $values = self::values($model, $attribute);

        if ($values === null) {
            return null;
        }

        $locale = app()->getLocale();

        return self::filled($values[$locale] ?? null) ? $values[$locale] : (self::filled($values['en'] ?? null) ? $values['en'] : null);
    }

    /** Required translatable field as a string. Empty only when no locale has a value. */
    public static function text(Model $model, string $attribute): string
    {
        return self::current($model, $attribute) ?? '';
    }

    /** Both locales, or null when neither is set. */
    public static function both(Model $model, string $attribute): ?array
    {
        $values = self::values($model, $attribute);

        if ($values === null) {
            return null;
        }

        $both = ['ar' => $values['ar'] ?? null, 'en' => $values['en'] ?? null];

        return self::filled($both['ar']) || self::filled($both['en']) ? $both : null;
    }

    private static function values(Model $model, string $attribute): ?array
    {
        $raw = $model->getRawOriginal($attribute);

        if ($raw === null) {
            return null;
        }

        $decoded = is_array($raw) ? $raw : json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    private static function filled(mixed $value): bool
    {
        return is_string($value) && $value !== '';
    }
}
