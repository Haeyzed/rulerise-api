<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
             * The ISO code of the currency.
             *
             * @var string $code
             * @example "USD"
             */
            'code' => $this->code,

            /**
             * The name of the currency.
             *
             * @var string $name
             * @example "US Dollar"
             */
            'name' => $this->name,

            /**
             * The symbol of the currency.
             *
             * @var string $symbol
             * @example "$"
             */
            'symbol' => $this->symbol,

            /**
             * The native symbol of the currency.
             *
             * @var string $native_symbol
             * @example "$"
             */
            'native_symbol' => $this->native_symbol,

            /**
             * The decimal digits of the currency.
             *
             * @var int $decimal_digits
             * @example 2
             */
            'decimal_digits' => $this->decimal_digits,

            /**
             * The rounding of the currency.
             *
             * @var int $rounding
             * @example 0
             */
            'rounding' => $this->rounding,

            /**
             * The plural name of the currency.
             *
             * @var string $name_plural
             * @example "US dollars"
             */
            'name_plural' => $this->name_plural,
        ];
    }
}