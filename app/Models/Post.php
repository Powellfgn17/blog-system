<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    public const TYPE_BLOG = 'BLOG';

    public const TYPE_COMMUNITY = 'COMMUNITY';

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'body',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'reading_time' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->isDirty('body')) {
                $post->reading_time = static::calculateReadingTime($post->body ?? '');
            }
        });
    }

    public static function calculateReadingTime(string $body): int
    {
        $wordCount = str_word_count(strip_tags($body));

        return (int) max(1, (int) ceil($wordCount / 200));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function rootComments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeBlog(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BLOG);
    }

    public function scopeCommunity(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_COMMUNITY);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public function isBlog(): bool
    {
        return $this->type === self::TYPE_BLOG;
    }

    public function isCommunity(): bool
    {
        return $this->type === self::TYPE_COMMUNITY;
    }

    public function getReadingTimeLabelAttribute(): string
    {
        $minutes = $this->reading_time ?? static::calculateReadingTime($this->body ?? '');

        return "Lecture : {$minutes} min";
    }
}
