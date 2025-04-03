<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
             * The unique identifier for the city.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the city.
             *
             * @var string $name
             * @example "Los Angeles"
             */
            'name' => $this->name,

            /**
             * The state ID of the city.
             *
             * @var int $state_id
             * @example 1
             */
            'state_id' => $this->state_id,

            /**
             * The state code of the city.
             *
             * @var string $state_code
             * @example "CA"
             */
            'state_code' => $this->state_code,

            /**
             * The country ID of the city.
             *
             * @var int $country_id
             * @example 1
             */
            'country_id' => $this->country_id,

            /**
             * The country code of the city.
             *
             * @var string $country_code
             * @example "US"
             */
            'country_code' => $this->country_code,

            /**
             * The latitude of the city.
             *
             * @var float $latitude
             * @example 34.052235
             */
            'latitude' => $this->latitude,

            /**
             * The longitude of the city.
             *
             * @var float $longitude
             * @example -118.243683
             */
            'longitude' => $this->longitude,

            /**
             * Whether the city is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,
        ];
    }
}