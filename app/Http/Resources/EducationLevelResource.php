<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationLevelResource extends JsonResource
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
             * The unique identifier for the education level.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the education level.
             *
             * @var string $name
             * @example "Bachelor's Degree"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the education level.
             *
             * @var string $slug
             * @example "bachelors-degree"
             */
            'slug' => $this->slug,

            /**
             * The jobs requiring this education level.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The candidate profiles with this education level.
             *
             * @var array|null $candidate_profiles
             */
            'candidate_profiles' => CandidateProfileResource::collection($this->whenLoaded('candidateProfiles')),

            /**
             * The timestamp when the education level was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the education level was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

