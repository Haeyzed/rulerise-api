<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimezoneResource extends JsonResource
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
             * The zone name of the timezone.
             *
             * @var string $zone_name
             * @example "America/Los_Angeles"
             */
            'zone_name' => $this->zone_name,

            /**
             * The GMT offset of the timezone.
             *
             * @var string $gmt_offset
             * @example "-08:00"
             */
            'gmt_offset' => $this->gmt_offset,

            /**
             * The GMT offset name of the timezone.
             *
             * @var string $gmt_offset_name
             * @example "UTC-08:00"
             */
            'gmt_offset_name' => $this->gmt_offset_name,

            /**
             * The abbreviation of the timezone.
             *
             * @var string $abbreviation
             * @example "PST"
             */
            'abbreviation' => $this->abbreviation,

            /**
             * The timezone name of the timezone.
             *
             * @var string $timezone_name
             * @example "Pacific Standard Time"
             */
            'timezone_name' => $this->timezone_name,
        ];
    }
}