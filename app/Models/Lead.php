<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPES = ['quote', 'demo', 'callback'];

    public const STATUSES = ['new', 'contacted', 'won', 'not_interested', 'spam'];

    protected $fillable = [
        'type',
        'status',
        'name',
        'mobile',
        'email',
        'company',
        'business_name',
        'service_id',
        'solution_id',
        'budget',
        'start_timing',
        'project_details',
        'preferred_contact_time',
        'need',
        'language',
        'page_url',
        'utm',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'utm' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
