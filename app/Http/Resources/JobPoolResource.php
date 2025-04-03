<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPoolResource extends JsonResource
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
             * The unique identifier for the job pool.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The company ID associated with this job pool.
             *
             * @var int $company_id
             * @example 1
             */
            'company_id' => $this->company_id,

            /**
             * The company details.
             *
             * @var array|null $company
             */
            'company' => new CompanyResource($this->whenLoaded('company')),

            /**
             * The name of the job pool.
             *
             * @var string $name
             * @example "Software Engineering Talent Pool"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the job pool.
             *
             * @var string $slug
             * @example "software-engineering-talent-pool"
             */
            'slug' => $this->slug,

            /**
             * The description of the job pool.
             *
             * @var string|null $description
             * @example "A collection of pre-screened software engineering candidates..."
             */
            'description' => $this->description,

            /**
             * Whether the job pool is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * Whether the job pool is public.
             *
             * @var bool $is_public
             * @example false
             */
            'is_public' => $this->is_public,

            /**
             * The start date of the job pool.
             *
             * @var string|null $start_date
             * @example "2023-01-01"
             */
            'start_date' => $this->start_date,

            /**
             * The end date of the job pool.
             *
             * @var string|null $end_date
             * @example "2023-12-31"
             */
            'end_date' => $this->end_date,

            /**
             * Whether the job pool has active dates.
             *
             * @var bool $is_active_dates
             * @example true
             */
            'is_active_dates' => $this->is_active_dates,

            /**
             * The target number of candidates to hire.
             *
             * @var int|null $target_hiring_count
             * @example 10
             */
            'target_hiring_count' => $this->target_hiring_count,

            /**
             * The current number of candidates hired.
             *
             * @var int $current_hiring_count
             * @example 3
             */
            'current_hiring_count' => $this->current_hiring_count,

            /**
             * The progress percentage towards the hiring target.
             *
             * @var int|null $progress_percentage
             * @example 30
             */
            'progress_percentage' => $this->progress_percentage,

            /**
             * The department associated with the job pool.
             *
             * @var string|null $department
             * @example "Engineering"
             */
            'department' => $this->department,

            /**
             * The location of the job pool.
             *
             * @var string|null $location
             * @example "San Francisco, CA"
             */
            'location' => $this->location,

            /**
             * The jobs associated with this job pool.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The skills required for this job pool.
             *
             * @var array|null $skills
             */
            'skills' => SkillResource::collection($this->whenLoaded('skills')),

            /**
             * The job pool skills with importance levels.
             *
             * @var array|null $job_pool_skills
             */
            'job_pool_skills' => JobPoolSkillResource::collection($this->whenLoaded('jobPoolSkills')),

            /**
             * The candidates in this job pool.
             *
             * @var array|null $candidates
             */
            'candidates' => CandidateProfileResource::collection($this->whenLoaded('candidates')),

            /**
             * The candidate job pools with status information.
             *
             * @var array|null $candidate_job_pools
             */
            'candidate_job_pools' => CandidateJobPoolResource::collection($this->whenLoaded('candidateJobPools')),

            /**
             * The timestamp when the job pool was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the job pool was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the job pool was deleted (soft delete).
             *
             * @var string|null $deleted_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}

