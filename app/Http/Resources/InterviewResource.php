<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
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
             * The unique identifier for the interview.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The application ID associated with this interview.
             *
             * @var int $application_id
             * @example 1
             */
            'application_id' => $this->application_id,

            /**
             * The job application details.
             *
             * @var array|null $application
             */
            'application' => new JobApplicationResource($this->whenLoaded('application')),

            /**
             * The user ID of the person who scheduled the interview.
             *
             * @var int $scheduled_by
             * @example 5
             */
            'scheduled_by' => $this->scheduled_by,

            /**
             * The user who scheduled the interview.
             *
             * @var array|null $scheduled_by_user
             */
            'scheduled_by_user' => new UserResource($this->whenLoaded('scheduledBy')),

            /**
             * The date and time of the interview.
             *
             * @var string $interview_date
             * @example "2023-02-15T14:00:00.000000Z"
             */
            'interview_date' => $this->interview_date,

            /**
             * The formatted interview date and time.
             *
             * @var string $formatted_interview_date
             * @example "Feb 15, 2023 at 2:00 PM"
             */
            'formatted_interview_date' => $this->formatted_interview_date,

            /**
             * The duration of the interview in minutes.
             *
             * @var int|null $duration_minutes
             * @example 60
             */
            'duration_minutes' => $this->duration_minutes,

            /**
             * The formatted duration of the interview.
             *
             * @var string $formatted_duration
             * @example "1 hour"
             */
            'formatted_duration' => $this->formatted_duration,

            /**
             * The location of the interview.
             *
             * @var string|null $location
             * @example "Company HQ, 123 Main Street, Floor 5"
             */
            'location' => $this->location,

            /**
             * The meeting link for online interviews.
             *
             * @var string|null $meeting_link
             * @example "https://zoom.us/j/123456789"
             */
            'meeting_link' => $this->meeting_link,

            /**
             * Whether the interview is online.
             *
             * @var bool $is_online
             * @example true
             */
            'is_online' => $this->is_online,

            /**
             * Additional notes about the interview.
             *
             * @var string|null $notes
             * @example "Please prepare a 15-minute presentation on your past projects."
             */
            'notes' => $this->notes,

            /**
             * The status of the interview.
             *
             * @var string $status
             * @example "scheduled"
             */
            'status' => $this->status,

            /**
             * The human-readable label for the status.
             *
             * @var string|null $status_label
             * @example "Scheduled"
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
             * Whether the interview is upcoming.
             *
             * @var bool $is_upcoming
             * @example true
             */
            'is_upcoming' => $this->is_upcoming,

            /**
             * Whether the interview is in the past.
             *
             * @var bool $is_past
             * @example false
             */
            'is_past' => $this->is_past,

            /**
             * The timestamp when the interview was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the interview was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

