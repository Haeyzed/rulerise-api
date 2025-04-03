<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPoolSkillResource extends JsonResource
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
             * The unique identifier for the job pool skill.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The job pool ID associated with this skill.
             *
             * @var int $job_pool_id
             * @example 1
             */
            'job_pool_id' => $this->job_pool_id,

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
             * The importance level of the skill.
             *
             * @var string $importance
             * @example "required"
             */
            'importance' => $this->importance,

            /**
             * The human-readable label for the importance level.
             *
             * @var string|null $importance_label
             * @example "Required"
             */
            'importance_label' => $this->importance_label,

            /**
             * The color code associated with the importance level.
             *
             * @var string|null $importance_color
             * @example "#dc3545"
             */
            'importance_color' => $this->importance_color,

            /**
             * The timestamp when the job pool skill was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,
        ];
    }
}

