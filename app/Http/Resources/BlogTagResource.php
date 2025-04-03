<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagResource extends JsonResource
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
             * The unique identifier for the blog tag.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the blog tag.
             *
             * @var string $name
             * @example "Interview Tips"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the blog tag.
             *
             * @var string $slug
             * @example "interview-tips"
             */
            'slug' => $this->slug,

            /**
             * The count of published posts with this tag.
             *
             * @var int $published_posts_count
             * @example 12
             */
            'published_posts_count' => $this->published_posts_count,

            /**
             * The blog posts with this tag.
             *
             * @var array|null $posts
             */
            'posts' => BlogPostResource::collection($this->whenLoaded('posts')),

            /**
             * The timestamp when the blog tag was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the blog tag was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

