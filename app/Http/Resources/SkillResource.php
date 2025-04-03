<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
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
             * The unique identifier for the skill.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the skill.
             *
             * @var string $name
             * @example "JavaScript"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the skill.
             *
             * @var string $slug
             * @example "javascript"
             */
            'slug' => $this->slug,

            /**
             * The jobs requiring this skill.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The candidates with this skill.
             *
             * @var array|null $candidates
             */
            'candidates' => CandidateProfileResource::collection($this->whenLoaded('candidates')),

            /**
             * The job pools requiring this skill.
             *
             * @var array|null $job_pools
             */
            'job_pools' => JobPoolResource::collection($this->whenLoaded('jobPools')),

            /**
             * The timestamp when the skill was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the skill was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

