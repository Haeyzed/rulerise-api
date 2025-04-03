<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPoolStatusHistoryResource extends JsonResource
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
             * The unique identifier for the job pool status history.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate job pool ID associated with this status history.
             *
             * @var int $candidate_job_pool_id
             * @example 1
             */
            'candidate_job_pool_id' => $this->candidate_job_pool_id,

            /**
             * The candidate job pool details.
             *
             * @var array|null $candidate_job_pool
             */
            'candidate_job_pool' => new CandidateJobPoolResource($this->whenLoaded('candidateJobPool')),

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
             * Additional notes about the status change.
             *
             * @var string|null $notes
             * @example "Candidate performed well in technical assessment."
             */
            'notes' => $this->notes,

            /**
             * The user ID of the person who changed the status.
             *
             * @var int|null $changed_by_user_id
             * @example 5
             */
            'changed_by_user_id' => $this->changed_by_user_id,

            /**
             * The user who changed the status.
             *
             * @var array|null $changed_by_user
             */
            'changed_by_user' => new UserResource($this->whenLoaded('changedByUser')),

            /**
             * The timestamp when the status was changed.
             *
             * @var string $created_at
             * @example "2023-01-15T14:30:00.000000Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}

