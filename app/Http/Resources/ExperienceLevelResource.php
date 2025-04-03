<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceLevelResource extends JsonResource
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
             * The unique identifier for the experience level.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the experience level.
             *
             * @var string $name
             * @example "Senior"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the experience level.
             *
             * @var string $slug
             * @example "senior"
             */
            'slug' => $this->slug,

            /**
             * The jobs with this experience level.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The timestamp when the experience level was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the experience level was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

