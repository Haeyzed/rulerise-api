<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
             * The unique identifier for the country.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the country.
             *
             * @var string $name
             * @example "United States"
             */
            'name' => $this->name,

            /**
             * The ISO code of the country.
             *
             * @var string $iso2
             * @example "USA"
             */
            'code' => $this->iso2,

            /**
             * The phone code of the country.
             *
             * @var string|null $phone_code
             * @example "+1"
             */
            'phone_code' => $this->phone_code,

            /**
             * The currency code of the country.
             *
             * @var string|null $currency
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The currency symbol of the country.
             *
             * @var string|null $currency_symbol
             * @example "$"
             */
            'currency_symbol' => $this->currency_symbol,

            /**
             * The flag image path of the country.
             *
             * @var string|null $flag
             * @example "flags/us.png"
             */
            'flag' => $this->flag,

            /**
             * Whether the country is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The states belonging to this country.
             *
             * @var array|null $states
             */
            'states' => StateResource::collection($this->whenLoaded('states')),

            /**
             * The timestamp when the country was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the country was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

