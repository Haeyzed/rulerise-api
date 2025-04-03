<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateEducationResource extends JsonResource
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
             * The unique identifier for the candidate education.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this education.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The name of the educational institution.
             *
             * @var string $institution
             * @example "Stanford University"
             */
            'institution' => $this->institution,

            /**
             * The degree obtained.
             *
             * @var string $degree
             * @example "Bachelor of Science"
             */
            'degree' => $this->degree,

            /**
             * The field of study.
             *
             * @var string|null $field_of_study
             * @example "Computer Science"
             */
            'field_of_study' => $this->field_of_study,

            /**
             * The formatted degree and field of study.
             *
             * @var string $formatted_degree
             * @example "Bachelor of Science in Computer Science"
             */
            'formatted_degree' => $this->formatted_degree,

            /**
             * The start date of the education.
             *
             * @var string $start_date
             * @example "2015-09-01"
             */
            'start_date' => $this->start_date,

            /**
             * The end date of the education.
             *
             * @var string|null $end_date
             * @example "2019-06-30"
             */
            'end_date' => $this->end_date,

            /**
             * Whether this is the current education.
             *
             * @var bool $is_current
             * @example false
             */
            'is_current' => $this->is_current,

            /**
             * The duration of the education (e.g., "2015 - 2019").
             *
             * @var string $duration
             * @example "2015 - 2019"
             */
            'duration' => $this->duration,

            /**
             * The description of the education.
             *
             * @var string|null $description
             * @example "Focused on algorithms and machine learning..."
             */
            'description' => $this->description,

            /**
             * The grade or GPA achieved.
             *
             * @var string|null $grade
             * @example "3.8/4.0"
             */
            'grade' => $this->grade,

            /**
             * The activities participated in during education.
             *
             * @var string|null $activities
             * @example "Computer Science Club, Hackathons, Research Assistant"
             */
            'activities' => $this->activities,

            /**
             * The timestamp when the education record was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the education record was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

