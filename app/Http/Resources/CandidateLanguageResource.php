<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateLanguageResource extends JsonResource
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
             * The unique identifier for the candidate language.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this language.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The language ID.
             *
             * @var int $language_id
             * @example 1
             */
            'language_id' => $this->language_id,

            /**
             * The language details.
             *
             * @var array|null $language
             */
            'language' => new LanguageResource($this->whenLoaded('language')),

            /**
             * The language name (convenience accessor).
             *
             * @var string|null $language_name
             * @example "English"
             */
            'language_name' => $this->language_name,

            /**
             * The proficiency level of the language.
             *
             * @var string $proficiency
             * @example "advanced"
             */
            'proficiency' => $this->proficiency,

            /**
             * The human-readable label for the proficiency level.
             *
             * @var string|null $proficiency_label
             * @example "Fluent"
             */
            'proficiency_label' => $this->proficiency_label,

            /**
             * The color code associated with the proficiency level.
             *
             * @var string|null $proficiency_color
             * @example "#007bff"
             */
            'proficiency_color' => $this->proficiency_color,

            /**
             * The timestamp when the candidate language was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the candidate language was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

