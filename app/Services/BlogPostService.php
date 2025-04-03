<?php

namespace App\Services;

use App\Enums\BlogPostStatusEnum;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Services\Storage\StorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogPostService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * BlogPostService constructor.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * List blog posts based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return BlogPost::query()
            ->with(['category', 'user', 'tags'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['user_id']), function ($query) use ($filters) {
                $query->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['tag_id']), function ($query) use ($filters) {
                $query->whereHas('tags', function ($q) use ($filters) {
                    $q->where('tag_id', $filters['tag_id']);
                });
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['is_published']), function ($query) use ($filters) {
                $query->where('is_published', $filters['is_published']);
            })
            ->when(isset($filters['published_after']), function ($query) use ($filters) {
                $query->where('published_at', '>=', $filters['published_after']);
            })
            ->when(isset($filters['published_before']), function ($query) use ($filters) {
                $query->where('published_at', '<=', $filters['published_before']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest('published_at');
            })
            ->paginate($perPage);
    }

    /**
     * Create a new blog post.
     *
     * @param array $data
     * @return BlogPost
     */
    public function create(array $data): BlogPost
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['title']);

            // Handle featured image upload
            if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
                $data['featured_image'] = $this->uploadImage(
                    $data['featured_image'],
                    config('filestorage.paths.featured_images')
                );
            }

            // Set published_at if is_published is true
            if (isset($data['is_published']) && $data['is_published']) {
                $data['published_at'] = $data['published_at'] ?? now();
                $data['status'] = BlogPostStatusEnum::PUBLISHED->value;
            } else {
                $data['status'] = BlogPostStatusEnum::DRAFT->value;
            }

            // Create blog post
            $blogPost = BlogPost::query()->create($data);

            // Attach tags if provided
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $blogPost->tags()->attach($data['tag_ids']);
            }

            // Create tags from tag names if provided
            if (isset($data['tag_names']) && is_array($data['tag_names'])) {
                $tagIds = [];

                foreach ($data['tag_names'] as $tagName) {
                    $tag = BlogTag::query()->firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );

                    $tagIds[] = $tag->id;
                }

                if (!empty($tagIds)) {
                    $blogPost->tags()->syncWithoutDetaching($tagIds);
                }
            }

            return $blogPost;
        });
    }

    /**
     * Update an existing blog post.
     *
     * @param BlogPost $blogPost
     * @param array $data
     * @return BlogPost
     */
    public function update(BlogPost $blogPost, array $data): BlogPost
    {
        return DB::transaction(function () use ($blogPost, $data) {
            // Generate slug if title is changed
            if (isset($data['title']) && $data['title'] !== $blogPost->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $blogPost->id);
            }

            // Handle featured image upload
            if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
                // Delete old featured image if exists
                if ($blogPost->featured_image) {
                    $this->storageService->delete($blogPost->featured_image);
                }

                $data['featured_image'] = $this->uploadImage(
                    $data['featured_image'],
                    config('filestorage.paths.featured_images')
                );
            }

            // Set published_at if is_published is true and not previously published
            if (isset($data['is_published'])) {
                if ($data['is_published'] && !$blogPost->published_at) {
                    $data['published_at'] = $data['published_at'] ?? now();
                    $data['status'] = BlogPostStatusEnum::PUBLISHED->value;
                } elseif (!$data['is_published']) {
                    $data['status'] = BlogPostStatusEnum::DRAFT->value;
                }
            }

            // Update blog post
            $blogPost->update($data);

            // Sync tags if provided
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $blogPost->tags()->sync($data['tag_ids']);
            }

            // Create tags from tag names if provided
            if (isset($data['tag_names']) && is_array($data['tag_names'])) {
                $tagIds = [];

                foreach ($data['tag_names'] as $tagName) {
                    $tag = BlogTag::query()->firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );

                    $tagIds[] = $tag->id;
                }

                if (!empty($tagIds)) {
                    // If tag_ids is also provided, merge the arrays
                    if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                        $tagIds = array_merge($tagIds, $data['tag_ids']);
                    }

                    $blogPost->tags()->sync($tagIds);
                }
            }

            return $blogPost;
        });
    }

    /**
     * Delete a blog post.
     *
     * @param BlogPost $blogPost
     * @return bool
     */
    public function delete(BlogPost $blogPost): bool
    {
        return DB::transaction(function () use ($blogPost) {
            // Delete featured image if exists
            if ($blogPost->featured_image) {
                $this->storageService->delete($blogPost->featured_image);
            }

            // Detach tags
            $blogPost->tags()->detach();

            // Delete blog post
            return $blogPost->delete();
        });
    }

    /**
     * Publish a blog post.
     *
     * @param BlogPost $blogPost
     * @param \DateTime|null $publishedAt
     * @return BlogPost
     */
    public function publish(BlogPost $blogPost, ?\DateTime $publishedAt = null): BlogPost
    {
        $blogPost->update([
            'is_published' => true,
            'published_at' => $publishedAt ?? now(),
            'status' => BlogPostStatusEnum::PUBLISHED,
        ]);

        return $blogPost;
    }

    /**
     * Unpublish a blog post.
     *
     * @param BlogPost $blogPost
     * @return BlogPost
     */
    public function unpublish(BlogPost $blogPost): BlogPost
    {
        $blogPost->update([
            'is_published' => false,
            'status' => BlogPostStatusEnum::DRAFT,
        ]);

        return $blogPost;
    }

    /**
     * Archive a blog post.
     *
     * @param BlogPost $blogPost
     * @return BlogPost
     */
    public function archive(BlogPost $blogPost): BlogPost
    {
        $blogPost->update([
            'status' => BlogPostStatusEnum::ARCHIVED,
        ]);

        return $blogPost;
    }

    /**
     * Increment the views count of a blog post.
     *
     * @param BlogPost $blogPost
     * @return BlogPost
     */
    public function incrementViewsCount(BlogPost $blogPost): BlogPost
    {
        $blogPost->increment('views_count');
        return $blogPost;
    }

    /**
     * Get related blog posts.
     *
     * @param BlogPost $blogPost
     * @param int $limit
     * @return Collection
     */
    public function getRelatedPosts(BlogPost $blogPost, int $limit = 5): Collection
    {
        // Get blog post tags
        $tagIds = $blogPost->tags()->pluck('tag_id')->toArray();

        // Find related posts based on category and tags
        return BlogPost::query()
            ->with(['category', 'user', 'tags'])
            ->where('id', '!=', $blogPost->id)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->where(function ($query) use ($blogPost, $tagIds) {
                $query->where('category_id', $blogPost->category_id)
                    ->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tag_id', $tagIds);
                    });
            })
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get popular blog posts.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularPosts(int $limit = 5): Collection
    {
        return BlogPost::query()
            ->with(['category', 'user', 'tags'])
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('views_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get blog post statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => BlogPost::query()->count(),
            'published' => BlogPost::query()->where('is_published', true)->count(),
            'draft' => BlogPost::query()->where('is_published', false)->where('status', BlogPostStatusEnum::DRAFT->value)->count(),
            'archived' => BlogPost::query()->where('status', BlogPostStatusEnum::ARCHIVED->value)->count(),
            'total_views' => BlogPost::query()->sum('views_count'),
            'by_category' => DB::table('blog_posts')
                ->join('blog_categories', 'blog_posts.category_id', '=', 'blog_categories.id')
                ->select('blog_categories.name', DB::raw('count(*) as total'))
                ->groupBy('blog_categories.name')
                ->get(),
            'by_author' => DB::table('blog_posts')
                ->join('users', 'blog_posts.user_id', '=', 'users.id')
                ->select(
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as author_name'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('users.id', 'users.first_name', 'users.last_name')
                ->get(),
            'by_month' => DB::table('blog_posts')
                ->select(DB::raw('DATE_FORMAT(published_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
                ->whereNotNull('published_at')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get(),
            'popular_tags' => DB::table('blog_post_tags')
                ->join('blog_tags', 'blog_post_tags.tag_id', '=', 'blog_tags.id')
                ->select('blog_tags.name', DB::raw('count(*) as total'))
                ->groupBy('blog_tags.name')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'new_today' => BlogPost::query()->whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => BlogPost::query()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => BlogPost::query()->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * Generate a unique slug for a blog post.
     *
     * @param string $title
     * @param int|null $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $count = BlogPost::query()->where('slug', 'like', "{$slug}%")
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Upload an image to storage.
     *
     * @param UploadedFile $image The image file to upload.
     * @param string $path The storage path.
     * @param array $options Additional options for the upload.
     * @return string The path to the uploaded image.
     */
    private function uploadImage(UploadedFile $image, string $path, array $options = []): string
    {
        return $this->storageService->upload($image, $path, $options);
    }

    /**
     * Search blog posts.
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return BlogPost::query()
            ->with(['category', 'user', 'tags'])
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('category', function ($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  });
            })
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Generate SEO metadata for a blog post.
     *
     * @param BlogPost $blogPost
     * @return array
     */
    public function generateSeoMetadata(BlogPost $blogPost): array
    {
        return [
            'title' => $blogPost->title,
            'description' => $blogPost->excerpt ?? Str::limit(strip_tags($blogPost->content), 160),
            'keywords' => $blogPost->tags->pluck('name')->implode(', '),
            'author' => $blogPost->user->full_name,
            'published_time' => $blogPost->published_at?->toIso8601String(),
            'modified_time' => $blogPost->updated_at->toIso8601String(),
            'image' => $blogPost->featured_image_url,
            'url' => url('/blog/' . $blogPost->slug),
            'type' => 'article',
            'section' => $blogPost->category->name,
            'tags' => $blogPost->tags->pluck('name')->toArray(),
        ];
    }
}

