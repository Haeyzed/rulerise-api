<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the blog post.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The category ID of the blog post.
             *
             * @var int $category_id
             * @example 1
             */
            'category_id' => $this->category_id,

            /**
             * The category details of the blog post.
             *
             * @var array|null $category
             */
            'category' => new BlogCategoryResource($this->whenLoaded('category')),

            /**
             * The user ID of the author.
             *
             * @var int $user_id
             * @example 1
             */
            'user_id' => $this->user_id,

            /**
             * The author details of the blog post.
             *
             * @var array|null $user
             */
            'user' => new UserResource($this->whenLoaded('user')),

            /**
             * The title of the blog post.
             *
             * @var string $title
             * @example "10 Tips for Successful Job Hunting"
             */
            'title' => $this->title,

            /**
             * The URL-friendly slug of the blog post.
             *
             * @var string $slug
             * @example "10-tips-for-successful-job-hunting"
             */
            'slug' => $this->slug,

            /**
             * The content of the blog post.
             *
             * @var string $content
             * @example "<p>Finding a job can be challenging...</p>"
             */
            'content' => $this->content,

            /**
             * The excerpt or summary of the blog post.
             *
             * @var string|null $excerpt
             * @example "Discover effective strategies for finding your dream job in today's competitive market."
             */
            'excerpt' => $this->excerpt,

            /**
             * The featured image file path of the blog post.
             *
             * @var string|null $featured_image
             * @example "blog-images/post_1234567890.jpg"
             */
            'featured_image' => $this->featured_image,

            /**
             * The full URL to the blog post's featured image.
             *
             * @var string|null $featured_image_url
             * @example "https://example.com/storage/blog-images/post_1234567890.jpg"
             */
            'featured_image_url' => $this->featured_image_url,

            /**
             * Whether the blog post is published.
             *
             * @var bool $is_published
             * @example true
             */
            'is_published' => $this->is_published,

            /**
             * The date and time when the blog post was published.
             *
             * @var string|null $published_at
             * @example "2023-01-15T10:00:00.000000Z"
             */
            'published_at' => $this->published_at,

            /**
             * The number of views the blog post has received.
             *
             * @var int $views_count
             * @example 1250
             */
            'views_count' => $this->views_count,

            /**
             * The estimated reading time in minutes.
             *
             * @var int $reading_time
             * @example 5
             */
            'reading_time' => $this->reading_time,

            /**
             * The status of the blog post.
             *
             * @var string|null $status
             * @example "published"
             */
            'status' => $this->status,

            /**
             * The human-readable label for the blog post status.
             *
             * @var string|null $status_label
             * @example "Published"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The tags associated with the blog post.
             *
             * @var array|null $tags
             */
            'tags' => BlogTagResource::collection($this->whenLoaded('tags')),

            /**
             * The timestamp when the blog post was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the blog post was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

