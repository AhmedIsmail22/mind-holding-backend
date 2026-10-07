<?php

namespace App\Models;

use App\Models\Concerns\ReleasesSlugOnDelete;
use App\Support\Seo\InvalidatesSitemap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class SolutionIndustry extends Model
{
    use HasFactory, HasTranslations, InvalidatesSitemap, ReleasesSlugOnDelete, SoftDeletes;

    public array $translatable = ['name'];

    protected $fillable = ['name', 'slug', 'slug_ar', 'order'];

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }
}
