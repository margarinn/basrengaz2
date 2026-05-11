<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Relationships ───────────────────────────────────

    /**
     * Get all comments for this product.
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
     * Scope to only active (visible) products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order, then by name.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Accessors ───────────────────────────────────────

    /**
     * Get the full URL for the product image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * Get the price formatted as Indonesian Rupiah.
     * Example: "Rp 15.000"
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the average rating from approved comments.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->approvedComments()
            ->whereNotNull('rating')
            ->avg('rating');

        return $avg ? round($avg, 1) : null;
    }

    /**
     * Get the total number of ratings.
     */
    public function getRatingCountAttribute(): int
    {
        return $this->approvedComments()
            ->whereNotNull('rating')
            ->count();
    }
}
