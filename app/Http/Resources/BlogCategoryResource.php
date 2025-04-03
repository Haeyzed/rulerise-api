<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
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
             * The unique identifier for the blog category.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the blog category.
             *
             * @var string $name
             * @example "Career Advice"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the blog category.
             *
             * @var string $slug
             * @example "career-advice"
             */
            'slug' => $this->slug,

            /**
             * The description of the blog category.
             *
             * @var string|null $description
             * @example "Tips and guidance for career development and job searching"
             */
            'description' => $this->description,

            /**
             * The count of published posts in this category.
             *
             * @var int $published_posts_count
             * @example 25
             */
            'published_posts_count' => $this->published_posts_count,

            /**
             * The blog posts in this category.
             *
             * @var array|null $posts
             */
            'posts' => BlogPostResource::collection($this->whenLoaded('posts')),

            /**
             * The timestamp when the blog category was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the blog category was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

