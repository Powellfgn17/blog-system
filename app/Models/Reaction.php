<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    public const TYPE_LIKE = 'like';

    public const TYPE_LOVE = 'love';

    public const TYPE_HAHA = 'haha';

    public const TYPE_SAD = 'sad';

    public const TYPE_WOW = 'wow';

    public const TYPES = [
        self::TYPE_LIKE,
        self::TYPE_LOVE,
        self::TYPE_HAHA,
        self::TYPE_SAD,
        self::TYPE_WOW,
    ];

    public const LABELS = [
        self::TYPE_LIKE => "J'aime",
        self::TYPE_LOVE => "J'adore",
        self::TYPE_HAHA => 'Haha',
        self::TYPE_SAD => 'Triste',
        self::TYPE_WOW => 'Wow',
    ];

    public const EMOJIS = [
        self::TYPE_LIKE => '👍',
        self::TYPE_LOVE => '❤️',
        self::TYPE_HAHA => '😂',
        self::TYPE_SAD => '😢',
        self::TYPE_WOW => '😮',
    ];

    protected $fillable = [
        'user_id',
        'reactable_id',
        'reactable_type',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getEmojiAttribute(): string
    {
        return self::EMOJIS[$this->type] ?? '';
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->type] ?? $this->type;
    }
}
