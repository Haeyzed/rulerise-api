<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateSkillResource extends JsonResource
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
             * The unique identifier for the candidate skill.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this skill.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The skill ID.
             *
             * @var int $skill_id
             * @example 1
             */
            'skill_id' => $this->skill_id,

            /**
             * The skill details.
             *
             * @var array|null $skill
             */
            'skill' => new SkillResource($this->whenLoaded('skill')),

            /**
             * The proficiency level of the skill.
             *
             * @var string|null $proficiency_level
             * @example "advanced"
             */
            'proficiency_level' => $this->proficiency_level,

            /**
             * The human-readable label for the proficiency level.
             *
             * @var string|null $proficiency_level_label
             * @example "Advanced"
             */
            'proficiency_level_label' => $this->proficiency_level_label,

            /**
             * The color code associated with the proficiency level.
             *
             * @var string|null $proficiency_level_color
             * @example "#007bff"
             */
            'proficiency_level_color' => $this->proficiency_level_color,

            /**
             * The timestamp when the candidate skill was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the candidate skill was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

