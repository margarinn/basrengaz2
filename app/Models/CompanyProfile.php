<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'map_embed_url',
        'phone',
        'email',
        'whatsapp',
        'instagram',
        'facebook',
        'tiktok',
        'logo',
        'hero_image',
        'additional_info',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'additional_info' => 'array',
        ];
    }

    // ── Accessors ───────────────────────────────────────

    /**
     * Get the full URL for the logo image.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    /**
     * Get the full URL for the hero/banner image.
     */
    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? Storage::url($this->hero_image) : null;
    }

    /**
     * Get the WhatsApp link (wa.me format).
     */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp) {
            return null;
        }

        // Remove non-numeric characters and ensure it starts with country code
        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        return "https://wa.me/{$number}";
    }

    /**
     * Get the Instagram profile URL.
     */
    public function getInstagramUrlAttribute(): ?string
    {
        if (!$this->instagram) {
            return null;
        }

        $handle = ltrim($this->instagram, '@');

        return "https://instagram.com/{$handle}";
    }

    // ── Static Helpers ──────────────────────────────────

    /**
     * Get the single company profile instance, or create a default one.
     * This ensures there's always exactly one profile record.
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['name' => 'BasrengAz']
        );
    }
}
