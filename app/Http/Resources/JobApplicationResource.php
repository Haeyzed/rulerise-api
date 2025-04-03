<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
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
             * The unique identifier for the job application.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The job ID associated with this application.
             *
             * @var int $job_id
             * @example 1
             */
            'job_id' => $this->job_id,

            /**
             * The job details associated with this application.
             *
             * @var array|null $job
             */
            'job' => new JobResource($this->whenLoaded('job')),

            /**
             * The candidate ID associated with this application.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The candidate details associated with this application.
             *
             * @var array|null $candidate
             */
            'candidate' => new CandidateProfileResource($this->whenLoaded('candidate')),

            /**
             * The resume ID associated with this application.
             *
             * @var int|null $resume_id
             * @example 1
             */
            'resume_id' => $this->resume_id,

            /**
             * The resume details associated with this application.
             *
             * @var array|null $resume
             */
            'resume' => new CandidateResumeResource($this->whenLoaded('resume')),

            /**
             * The cover letter for the application.
             *
             * @var string|null $cover_letter
             * @example "I am writing to express my interest in the Software Engineer position..."
             */
            'cover_letter' => $this->cover_letter,

            /**
             * The status of the application.
             *
             * @var string $status
             * @example "pending"
             */
            'status' => $this->status,

            /**
             * The human-readable label for the application status.
             *
             * @var string|null $status_label
             * @example "Pending Review"
             */
            'status_label' => $this->status ? $this->status->label() : null,

            /**
             * The color code associated with the application status.
             *
             * @var string|null $status_color
             * @example "#ffc107"
             */
            'status_color' => $this->status ? $this->status->color() : null,

            /**
             * The expected salary specified in the application.
             *
             * @var float|null $expected_salary
             * @example 90000
             */
            'expected_salary' => $this->expected_salary,

            /**
             * The currency of the expected salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => $this->salary_currency,

            /**
             * The date the candidate is available to start.
             *
             * @var string|null $availability_date
             * @example "2023-03-15"
             */
            'availability_date' => $this->availability_date,

            /**
             * Additional notes for the application.
             *
             * @var string|null $notes
             * @example "I am particularly interested in working with your AI team."
             */
            'notes' => $this->notes,

            /**
             * Whether the application has been viewed by the employer.
             *
             * @var bool $is_viewed
             * @example false
             */
            'is_viewed' => $this->is_viewed,

            /**
             * The date and time when the application was viewed.
             *
             * @var string|null $viewed_at
             * @example "2023-01-15T14:30:00.000000Z"
             */
            'viewed_at' => $this->viewed_at,

            /**
             * The status history of the application.
             *
             * @var array|null $status_history
             */
            'status_history' => JobApplicationStatusHistoryResource::collection($this->whenLoaded('statusHistory')),

            /**
             * The interviews scheduled for this application.
             *
             * @var array|null $interviews
             */
            'interviews' => InterviewResource::collection($this->whenLoaded('interviews')),

            /**
             * The messages related to this application.
             *
             * @var array|null $messages
             */
            'messages' => MessageResource::collection($this->whenLoaded('messages')),

            /**
             * The timestamp when the application was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the application was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

