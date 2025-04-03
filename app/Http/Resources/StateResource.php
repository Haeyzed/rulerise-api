<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
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
             * The unique identifier for the state.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the state.
             *
             * @var string $name
             * @example "California"
             */
            'name' => $this->name,

            /**
             * The country ID of the state.
             *
             * @var int $country_id
             * @example 1
             */
            'country_id' => $this->country_id,

            /**
             * The country code of the state.
             *
             * @var string $country_code
             * @example "US"
             */
            'country_code' => $this->country_code,

            /**
             * The state code of the state.
             *
             * @var string $state_code
             * @example "LA"
             */
            'state_code' => $this->state_code,

            /**
             * The type of the state (e.g., state, province).
             *
             * @var string $type
             * @example "state"
             */
            'type' => $this->type,

            /**
             * The latitude of the state.
             *
             * @var float $latitude
             * @example 36.778259
             */
            'latitude' => $this->latitude,

            /**
             * The longitude of the state.
             *
             * @var float $longitude
             * @example -119.417931
             */
            'longitude' => $this->longitude,

            /**
             * Whether the state is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,
        ];
    }
}
