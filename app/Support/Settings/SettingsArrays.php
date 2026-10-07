<?php

namespace App\Support\Settings;

use App\Models\Setting;

/**
 * Typed reads of Setting's two plain JSON-array columns. A bare
 * `$setting->social_links` is a cast-array attribute Scramble's static
 * analysis can't see through, so it infers an untyped array; going through
 * a method with an explicit generic return type gives it something to read.
 */
final class SettingsArrays
{
    /**
     * An empty PHP array and an empty JSON object are the same value in PHP,
     * so json_encode would otherwise render an unset social_links as `[]`
     * instead of `{}` - a mismatch against the object type declared below.
     *
     * @return array<string, string> Platform name (e.g. "facebook") to profile URL.
     */
    public static function socialLinks(Setting $setting): array|\stdClass
    {
        $links = (array) ($setting->social_links ?? []);

        return $links === [] ? new \stdClass : $links;
    }

    /** @return array<int, string> ISO 3166-1 alpha-2 country codes. */
    public static function gulfCountries(Setting $setting): array
    {
        return collect($setting->gulf_countries ?? [])
            ->map(fn (string $code): string => $code)
            ->values()
            ->all();
    }
}
