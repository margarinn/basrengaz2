<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'body',
        'rating',
        'is_approved',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    // ── Relationships ───────────────────────────────────

    /**
     * Get the user who posted this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent commentable model (Product or Article).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ──────────────────────────────────────────

    /**
     * Scope to only approved comments.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to only comments with a rating.
     */
    public function scopeWithRating(Builder $query): Builder
    {
        return $query->whereNotNull('rating');
    }

    /**
     * Scope to comments for a specific model type.
     */
    public function scopeForModel(Builder $query, string $modelClass, int $modelId): Builder
    {
        return $query->where('commentable_type', $modelClass)
                     ->where('commentable_id', $modelId);
    }
}
