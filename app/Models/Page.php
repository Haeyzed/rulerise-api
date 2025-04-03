<?php

namespace App\Models;

use App\Services\Storage\StorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'is_active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope a query to only include active pages.
     *
     * @param Builder<Page> $query
     * @return Builder<Page>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order pages by the order field.
     *
     * @param Builder<Page> $query
     * @return Builder<Page>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    /**
     * Get the meta title attribute with fallback to title.
     *
     * @return string
     */
    public function getMetaTitleWithFallbackAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * Get the meta description with a fallback to excerpt or truncated content.
     *
     * @return string|null
     */
    public function getMetaDescriptionWithFallbackAttribute(): ?string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->excerpt;
        }

        return $this->content ? substr(strip_tags($this->content), 0, 160) : null;
    }

    /**
     * Get the featured image URL attribute.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        return app(StorageService::class)->url($this->featured_image);
    }
}