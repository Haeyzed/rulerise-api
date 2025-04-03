<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateExperienceResource extends JsonResource
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
             * The unique identifier for the candidate experience.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this experience.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The name of the company.
             *
             * @var string $company_name
             * @example "Google"
             */
            'company_name' => $this->company_name,

            /**
             * The job title held.
             *
             * @var string $job_title
             * @example "Senior Software Engineer"
             */
            'job_title' => $this->job_title,

            /**
             * The start date of the experience.
             *
             * @var string $start_date
             * @example "2019-07-01"
             */
            'start_date' => $this->start_date,

            /**
             * The end date of the experience.
             *
             * @var string|null $end_date
             * @example "2022-12-31"
             */
            'end_date' => $this->end_date,

            /**
             * Whether this is the current job.
             *
             * @var bool $is_current
             * @example true
             */
            'is_current' => $this->is_current,

            /**
             * The duration of the experience (e.g., "Jul 2019 - Present").
             *
             * @var string $duration
             * @example "Jul 2019 - Present"
             */
            'duration' => $this->duration,

            /**
             * The duration in months.
             *
             * @var int $duration_in_months
             * @example 36
             */
            'duration_in_months' => $this->duration_in_months,

            /**
             * The formatted duration (e.g., "3 years").
             *
             * @var string $formatted_duration
             * @example "3 years"
             */
            'formatted_duration' => $this->formatted_duration,

            /**
             * The description of the experience.
             *
             * @var string|null $description
             * @example "Led a team of 5 engineers developing cloud-based solutions..."
             */
            'description' => $this->description,

            /**
             * The location of the job.
             *
             * @var string|null $location
             * @example "Mountain View, CA"
             */
            'location' => $this->location,

            /**
             * The timestamp when the experience record was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the experience record was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

