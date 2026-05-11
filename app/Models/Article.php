<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'body',
        'image',
        'is_published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // ── Boot ────────────────────────────────────────────

    /**
     * Boot the model.
     * Auto-generate slug from title when creating/updating.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = self::generateUniqueSlug($article->title);
            }

            // Auto-set published_at when publishing
            if ($article->is_published && !$article->published_at) {
                $article->published_at = now();
            }
        });

        static::updating(function (Article $article) {
            // If title changed and slug wasn't manually set, regenerate slug
            if ($article->isDirty('title') && !$article->isDirty('slug')) {
                $article->slug = self::generateUniqueSlug($article->title, $article->id);
            }

            // Auto-set published_at when publishing for the first time
            if ($article->isDirty('is_published') && $article->is_published && !$article->published_at) {
                $article->published_at = now();
            }
        });
    }

    /**
     * Generate a unique slug from the given title.
     */
    protected static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = self::withTrashed()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = self::withTrashed()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    // ── Relationships ───────────────────────────────────

    /**
     * Get the author (admin user) who wrote this article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all comments for this article.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get only approved comments.
     */
    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('is_approved', true);
    }

    // ── Scopes ──────────────────────────────────────────

    /**
     * Scope to only published articles.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope to order by most recent published date.
     */
    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->published()->orderByDesc('published_at');
    }

    // ── Accessors ───────────────────────────────────────

    /**
     * Get the full URL for the featured image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Get a short excerpt from the body (first 200 characters).
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->body), 200);
    }

    /**
     * Get the route key name for URL binding.
     * This makes routes use slug instead of id: /articles/{slug}
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
