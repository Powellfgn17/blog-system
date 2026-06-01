<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function wasEdited(): bool
    {
        return $this->updated_at->gt($this->created_at);
    }
}
