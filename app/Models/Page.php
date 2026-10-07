<?php

namespace App\Models;

use App\Support\Seo\InvalidatesSitemap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations, InvalidatesSitemap;

    public array $translatable = ['title', 'body'];

    protected $fillable = ['slug', 'slug_ar', 'title', 'body', 'is_draft'];

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
        ];
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
