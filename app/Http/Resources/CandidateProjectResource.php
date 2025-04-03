<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateProjectResource extends JsonResource
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
             * The unique identifier for the candidate project.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this project.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The title of the project.
             *
             * @var string $title
             * @example "E-commerce Platform"
             */
            'title' => $this->title,

            /**
             * The description of the project.
             *
             * @var string|null $description
             * @example "Developed a full-stack e-commerce platform using React and Node.js..."
             */
            'description' => $this->description,

            /**
             * The start date of the project.
             *
             * @var string|null $start_date
             * @example "2021-03-01"
             */
            'start_date' => $this->start_date,

            /**
             * The end date of the project.
             *
             * @var string|null $end_date
             * @example "2021-09-30"
             */
            'end_date' => $this->end_date,

            /**
             * Whether this is a current project.
             *
             * @var bool $is_current
             * @example false
             */
            'is_current' => $this->is_current,

            /**
             * The duration of the project (e.g., "Mar 2021 - Sep 2021").
             *
             * @var string|null $duration
             * @example "Mar 2021 - Sep 2021"
             */
            'duration' => $this->duration,

            /**
             * The URL of the project.
             *
             * @var string|null $url
             * @example "https://github.com/johndoe/ecommerce-platform"
             */
            'url' => $this->url,

            /**
             * The timestamp when the project record was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the project record was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

