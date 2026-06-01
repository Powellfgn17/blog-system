<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    public const REASON_SPAM = 'spam';

    public const REASON_HARASSMENT = 'harassment';

    public const REASON_OFFENSIVE = 'offensive';

    public const REASON_MISINFORMATION = 'misinformation';

    public const REASON_OTHER = 'other';

    public const REASONS = [
        self::REASON_SPAM,
        self::REASON_HARASSMENT,
        self::REASON_OFFENSIVE,
        self::REASON_MISINFORMATION,
        self::REASON_OTHER,
    ];

    public const REASON_LABELS = [
        self::REASON_SPAM => 'Spam',
        self::REASON_HARASSMENT => 'Harcèlement',
        self::REASON_OFFENSIVE => 'Contenu offensant',
        self::REASON_MISINFORMATION => 'Contenu faux / désinformation',
        self::REASON_OTHER => 'Autre',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_IGNORED = 'ignored';

    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASON_LABELS[$this->reason] ?? $this->reason;
    }
}
