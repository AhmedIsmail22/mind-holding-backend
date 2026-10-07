<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * CR-01 rebrand for rows that were seeded before the change. Brand names
     * are replaced inside locale-keyed JSON: English under "en", Arabic under
     * "ar". Only the exact seeded contact email is changed, so an address an
     * admin entered is left alone.
     */
    private const JSON_COLUMNS = [
        'settings' => ['company_name', 'address'],
        'seo_meta' => ['title', 'description'],
        'services' => ['name', 'summary', 'description'],
        'solutions' => ['name', 'audience', 'summary'],
        'solution_industries' => ['name'],
        'pages' => ['title', 'body'],
        'home_content' => ['hero_headline', 'hero_subheadline', 'closing_cta_headline', 'closing_cta_subheadline', 'differentiators', 'process_steps'],
        'faqs' => ['question', 'answer'],
        'technologies' => ['category'],
    ];

    private const EMAIL_REPLACEMENTS = [
        'info@mindholding.net' => 'info@bitcodak.com',
    ];

    public function up(): void
    {
        foreach (self::JSON_COLUMNS as $table => $columns) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            foreach (DB::table($table)->get() as $row) {
                $updates = [];

                foreach ($columns as $column) {
                    if (! isset($row->{$column}) || ! is_string($row->{$column})) {
                        continue;
                    }

                    $decoded = json_decode($row->{$column}, true);

                    if (! is_array($decoded)) {
                        continue;
                    }

                    $rebranded = $this->rebrand($decoded);

                    if ($rebranded !== $decoded) {
                        $updates[$column] = json_encode($rebranded, JSON_UNESCAPED_UNICODE);
                    }
                }

                foreach (['email', 'lead_notification_email'] as $column) {
                    if ($table === 'settings' && isset(self::EMAIL_REPLACEMENTS[$row->{$column} ?? ''])) {
                        $updates[$column] = self::EMAIL_REPLACEMENTS[$row->{$column}];
                    }
                }

                if ($updates !== []) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    public function down(): void
    {
        // Data-only change. The previous brand is not restored.
    }

    /** Replaces the brand name under "ar" keys with Arabic, and everything else with English. */
    private function rebrand(mixed $value, ?string $locale = null): mixed
    {
        if (is_array($value)) {
            $out = [];

            foreach ($value as $key => $item) {
                $out[$key] = $this->rebrand($item, is_string($key) && in_array($key, ['ar', 'en'], true) ? $key : $locale);
            }

            return $out;
        }

        if (! is_string($value)) {
            return $value;
        }

        if ($locale === 'ar') {
            return str_replace(['MIND Holding', 'مايند القابضة'], 'بيتكودك', $value);
        }

        return str_replace('MIND Holding', 'Bitcodak', $value);
    }
};
