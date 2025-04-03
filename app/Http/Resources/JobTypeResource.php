<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobTypeResource extends JsonResource
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
             * The unique identifier for the job type.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the job type.
             *
             * @var string $name
             * @example "Full-time"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the job type.
             *
             * @var string $slug
             * @example "full-time"
             */
            'slug' => $this->slug,

            /**
             * The jobs with this job type.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The timestamp when the job type was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the job type was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

