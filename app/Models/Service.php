<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'group',
        'name',
        'slug',
        'description',
        'deliverables',
        'delivery_steps',
        'is_published',
        'is_draft',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'deliverables' => 'array',
            'delivery_steps' => 'array',
            'is_published' => 'boolean',
            'is_draft' => 'boolean',
        ];
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable');
    }
}
