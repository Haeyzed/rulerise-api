<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResumeResource extends JsonResource
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
             * The unique identifier for the candidate resume.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this resume.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The title of the resume.
             *
             * @var string $title
             * @example "Software Engineer Resume"
             */
            'title' => $this->title,

            /**
             * The file path of the resume.
             *
             * @var string $file_path
             * @example "resumes/resume_1234567890.pdf"
             */
            'file_path' => $this->file_path,

            /**
             * The full URL to the resume file.
             *
             * @var string $file_url
             * @example "https://example.com/storage/resumes/resume_1234567890.pdf"
             */
            'file_url' => $this->file_url,

            /**
             * The size of the file in bytes.
             *
             * @var int|null $file_size
             * @example 256000
             */
            'file_size' => $this->file_size,

            /**
             * The formatted file size (e.g., "250 KB").
             *
             * @var string $formatted_file_size
             * @example "250 KB"
             */
            'formatted_file_size' => $this->formatted_file_size,

            /**
             * The MIME type of the file.
             *
             * @var string|null $file_type
             * @example "application/pdf"
             */
            'file_type' => $this->file_type,

            /**
             * Whether this is the primary resume.
             *
             * @var bool $is_primary
             * @example true
             */
            'is_primary' => $this->is_primary,

            /**
             * The timestamp when the resume was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the resume was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

