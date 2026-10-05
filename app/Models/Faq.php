<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public array $translatable = ['question', 'answer'];

    protected $fillable = [
        'faqable_type',
        'faqable_id',
        'question',
        'answer',
        'is_published',
        'is_draft',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_draft' => 'boolean',
        ];
    }

    public function faqable(): MorphTo
    {
        return $this->morphTo();
    }
}
