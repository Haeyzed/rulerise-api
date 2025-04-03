<?php

namespace App\Services;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Currency;
use App\Models\Timezone;
use App\Models\Language;
use App\Models\PhoneCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class WorldService
{
    /**
     * Cache expiration time in seconds.
     *
     * @var int
     */
    protected int $cacheExpiration = 86400; // 24 hours

    /**
     * Get all countries.
     *
     * @return Collection
     */
    public function getCountries(): Collection
    {
        return Cache::remember('world_countries', $this->cacheExpiration, function () {
            return Country::all();
        });
    }

    /**
     * Get a country by ISO code.
     *
     * @param string $code
     * @return Country|null
     */
    public function getCountryByCode(string $code): ?Country
    {
        return Country::query()->where('iso2', strtoupper($code))
            ->orWhere('iso3', strtoupper($code))
            ->first();
    }

    /**
     * Get states/provinces for a country.
     *
     * @param string $countryCode
     * @return Collection
     */
    public function getStates(string $countryCode): Collection
    {
        return Cache::remember("world_states_$countryCode", $this->cacheExpiration, function () use ($countryCode) {
            $country = Country::query()->where('country_code', strtoupper($countryCode))->first();

            if (!$country) {
                return collect();
            }

            return State::query()->where('country_id', $country->id)->get();
        });
    }

    /**
     * Get cities for a state.
     *
     * @param string $countryCode
     * @param string $stateCode
     * @return Collection
     */
    public function getCities(string $countryCode, string $stateCode): Collection
    {
        return Cache::remember("world_cities_{$countryCode}_$stateCode", $this->cacheExpiration, function () use ($countryCode, $stateCode) {
            $country = Country::query()->where('iso2', strtoupper($countryCode))->first();

            if (!$country) {
                return collect();
            }

            $state = State::query()->where('country_id', $country->id)
                ->where('state_code', strtoupper($stateCode))
                ->first();

            if (!$state) {
                return collect();
            }

            return City::query()->where('state_id', $state->id)->get();
        });
    }

    /**
     * Get all currencies.
     *
     * @return Collection
     */
    public function getCurrencies(): Collection
    {
        return Cache::remember('world_currencies', $this->cacheExpiration, function () {
            return Currency::all();
        });
    }

    /**
     * Get a currency by code.
     *
     * @param string $code
     * @return Currency|null
     */
    public function getCurrencyByCode(string $code): ?Currency
    {
        return Currency::query()->where('code', strtoupper($code))->first();
    }

    /**
     * Get all timezones.
     *
     * @return Collection
     */
    public function getTimezones(): Collection
    {
        return Cache::remember('world_timezones', $this->cacheExpiration, function () {
            return Timezone::all();
        });
    }

    /**
     * Get timezones for a country.
     *
     * @param string $countryCode
     * @return Collection
     */
    public function getTimezonesByCountry(string $countryCode): Collection
    {
        return Cache::remember("world_timezones_$countryCode", $this->cacheExpiration, function () use ($countryCode) {
            $country = Country::query()->where('iso2', strtoupper($countryCode))->first();

            if (!$country) {
                return collect();
            }

            return Timezone::query()->where('country_id', $country->id)->get();
        });
    }

    /**
     * Get all languages.
     *
     * @return Collection
     */
    public function getLanguages(): Collection
    {
        return Cache::remember('world_languages', $this->cacheExpiration, function () {
            return Language::all();
        });
    }

    /**
     * Get a language by code.
     *
     * @param string $code
     * @return Language|null
     */
    public function getLanguageByCode(string $code): ?Language
    {
        return Language::query()->where('code', strtolower($code))->first();
    }

    /**
     * Get country phone codes.
     *
     * @return Collection
     */
    public function getPhoneCodes(): Collection
    {
        return Cache::remember('world_phone_codes', $this->cacheExpiration, function () {
            return PhoneCode::all();
        });
    }

    /**
     * Get a phone code by country code.
     *
     * @param string $countryCode
     * @return string|null
     */
    public function getPhoneCodeByCountry(string $countryCode): ?string
    {
        $phoneCode = PhoneCode::query()->whereHas('country', function ($query) use ($countryCode) {
            $query->where('iso2', strtoupper($countryCode));
        })->first();

        return $phoneCode ? $phoneCode->code : null;
    }

    /**
     * Clear all world data cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('world_countries');
        Cache::forget('world_currencies');
        Cache::forget('world_timezones');
        Cache::forget('world_languages');
        Cache::forget('world_phone_codes');

        // Clear state and city caches
        $countries = Country::all();

        foreach ($countries as $country) {
            $countryCode = $country->iso2;
            Cache::forget("world_states_$countryCode");

            $states = State::query()->where('country_id', $country->id)->get();

            foreach ($states as $state) {
                $stateCode = $state->iso2;
                Cache::forget("world_cities_{$countryCode}_$stateCode");
            }

            Cache::forget("world_timezones_$countryCode");
        }
    }
}
