<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobCategoryResource extends JsonResource
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
             * The unique identifier for the job category.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the job category.
             *
             * @var string $name
             * @example "Information Technology"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the job category.
             *
             * @var string $slug
             * @example "information-technology"
             */
            'slug' => $this->slug,

            /**
             * The description of the job category.
             *
             * @var string|null $description
             * @example "Jobs related to IT, software development, and technical support"
             */
            'description' => $this->description,

            /**
             * The icon of the job category.
             *
             * @var string|null $icon
             * @example "fa-laptop-code"
             */
            'icon' => $this->icon,

            /**
             * The URL to the job category's icon.
             *
             * @var string|null $icon_url
             * @example "https://example.com/storage/icons/it.png"
             */
            'icon_url' => $this->icon_url,

            /**
             * Whether the job category is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The jobs in this category.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The timestamp when the job category was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the job category was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

