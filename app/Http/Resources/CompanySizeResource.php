<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanySizeResource extends JsonResource
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
             * The unique identifier for the company size.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the company size.
             *
             * @var string $name
             * @example "Medium Enterprise"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the company size.
             *
             * @var string $slug
             * @example "medium-enterprise"
             */
            'slug' => $this->slug,

            /**
             * The employee range of the company size.
             *
             * @var string|null $range
             * @example "51-200 employees"
             */
            'range' => $this->range,

            /**
             * The description of the company size.
             *
             * @var string|null $description
             * @example "Companies with 51 to 200 employees"
             */
            'description' => $this->description,

            /**
             * The timestamp when the company size was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the company size was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

