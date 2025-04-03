<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateJobPoolResource extends JsonResource
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
             * The unique identifier for the candidate job pool.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this job pool.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The candidate details.
             *
             * @var array|null $candidate
             */
            'candidate' => new CandidateProfileResource($this->whenLoaded('candidate')),

            /**
             * The job pool ID.
             *
             * @var int $job_pool_id
             * @example 1
             */
            'job_pool_id' => $this->job_pool_id,

            /**
             * The job pool details.
             *
             * @var array|null $job_pool
             */
            'job_pool' => new JobPoolResource($this->whenLoaded('jobPool')),

            /**
             * The status of the candidate in the job pool.
             *
             * @var string $status
             * @example "shortlisted"
             */
            'status' => $this->status,

            /**
             * The human-readable label for the status.
             *
             * @var string|null $status_label
             * @example "Shortlisted"
             */
            'status_label' => $this->status_label,

            /**
             * The color code associated with the status.
             *
             * @var string|null $status_color
             * @example "#007bff"
             */
            'status_color' => $this->status_color,

            /**
             * Additional notes about the candidate in the job pool.
             *
             * @var string|null $notes
             * @example "Strong technical skills, good cultural fit."
             */
            'notes' => $this->notes,

            /**
             * The user ID of the person who added the candidate to the job pool.
             *
             * @var int|null $added_by_user_id
             * @example 5
             */
            'added_by_user_id' => $this->added_by_user_id,

            /**
             * The user who added the candidate to the job pool.
             *
             * @var array|null $added_by_user
             */
            'added_by_user' => new UserResource($this->whenLoaded('addedByUser')),

            /**
             * The status history of the candidate in the job pool.
             *
             * @var array|null $status_history
             */
            'status_history' => JobPoolStatusHistoryResource::collection($this->whenLoaded('statusHistory')),

            /**
             * The timestamp when the candidate was added to the job pool.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the candidate's status in the job pool was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

