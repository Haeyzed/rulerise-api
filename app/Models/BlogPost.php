<?php

namespace App\Models;

use App\Enums\BlogPostStatusEnum;
use App\Services\Storage\StorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class BlogPost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'is_published',
        'published_at',
        'views_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'status' => BlogPostStatusEnum::class,
    ];

    /**
     * Get the category that owns the blog post.
     *
     * @return BelongsTo<BlogCategory, BlogPost>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the user that owns the blog post.
     *
     * @return BelongsTo<User, BlogPost>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags for the blog post.
     *
     * @return BelongsToMany<BlogTag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tags', 'post_id', 'tag_id')
            ->withTimestamps();
    }

    /**
     * Get the featured image URL attribute.
     *
     * @return string|null
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        return app(StorageService::class)->url($this->featured_image);
    }

    /**
     * Get the reading time attribute.
     *
     * @return int
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // Assuming 200 words per minute reading speed
        return max(1, $readingTime); // Minimum 1 minute
    }

    /**
     * Scope a query to only include published posts.
     *
     * @param Builder<BlogPost> $query
     * @return Builder<BlogPost>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft posts.
     *
     * @param Builder<BlogPost> $query
     * @return Builder<BlogPost>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_published', false);
    }

    /**
     * Increment the views count.
     *
     * @return void
     */
    public function incrementViewsCount(): void
    {
        $this->increment('views_count');
    }
}

