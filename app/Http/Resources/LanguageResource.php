<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
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
             * The unique identifier for the language.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the language.
             *
             * @var string $name
             * @example "English"
             */
            'name' => $this->name,

            /**
             * The ISO code of the language.
             *
             * @var string $code
             * @example "en"
             */
            'code' => $this->code,

            /**
             * The native name of the language.
             *
             * @var string|null $native_name
             * @example "English"
             */
            'native_name' => $this->native_name,

            /**
             * Whether the language is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The timestamp when the language was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the language was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

