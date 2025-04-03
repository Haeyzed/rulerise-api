<?php

namespace App\Http\Requests;

use App\Enums\BlogPostStatusEnum;
use Illuminate\Validation\Rule;

class BlogPostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The blog category ID.
             *
             * @var int $category_id
             * @example 1
             */
            'category_id' => ['required', 'exists:blog_categories,id'],
            
            /**
             * The user ID of the author.
             *
             * @var int $user_id
             * @example 1
             */
            'user_id' => ['required', 'exists:users,id'],
            
            /**
             * The title of the blog post.
             *
             * @var string $title
             * @example "10 Tips for Successful Job Hunting"
             */
            'title' => ['required', 'string', 'max:255'],
            
            /**
             * The content of the blog post.
             *
             * @var string $content
             * @example "<p>Finding a job can be challenging...</p>"
             */
            'content' => ['required', 'string'],
            
            /**
             * The excerpt or summary of the blog post.
             *
             * @var string|null $excerpt
             * @example "Discover effective strategies for finding your dream job in today's competitive market."
             */
            'excerpt' => ['nullable', 'string'],
            
            /**
             * The featured image for the blog post.
             *
             * @var file|null $featured_image
             */
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            /**
             * Whether the blog post is published.
             *
             * @var bool $is_published
             * @example true
             */
            'is_published' => ['nullable', 'boolean'],
            
            /**
             * The date and time when the blog post was published.
             *
             * @var string|null $published_at
             * @example "2023-01-15T10:00:00.000000Z"
             */
            'published_at' => ['nullable', 'date'],
            
            /**
             * The status of the blog post.
             *
             * @var string|null $status
             * @example "published"
             */
            'status' => ['nullable', 'string', Rule::in(BlogPostStatusEnum::values())],
            
            /**
             * The tag IDs associated with the blog post.
             *
             * @var array|null $tag_ids
             * @example [1, 3, 5]
             */
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:blog_tags,id'],
            
            /**
             * The tag names to create and associate with the blog post.
             *
             * @var array|null $tag_names
             * @example ["Career Advice", "Interview Tips", "Resume Building"]
             */
            'tag_names' => ['nullable', 'array'],
            'tag_names.*' => ['string', 'max:100'],
        ];
    }
}

