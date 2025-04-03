<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\TimezoneResource;
use App\Services\WorldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorldController extends Controller
{
    /**
     * The world service instance.
     *
     * @var WorldService
     */
    protected WorldService $worldService;

    /**
     * Create a new controller instance.
     *
     * @param WorldService $worldService
     * @return void
     */
    public function __construct(WorldService $worldService)
    {
        $this->worldService = $worldService;
    }

    /**
     * Get all countries.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: CountryResource[]
     *  }
     */
    public function getCountries(): JsonResponse
    {
        $countries = $this->worldService->getCountries();

        return response()->success(
            CountryResource::collection($countries),
            'Countries retrieved successfully'
        );
    }

    /**
     * Get a country by ISO code.
     *
     * @param string $code
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: CountryResource
     *  }
     */
    public function getCountryByCode(string $code): JsonResponse
    {
        $country = $this->worldService->getCountryByCode($code);

        if (!$country) {
            return response()->error('Country not found', 404);
        }

        return response()->success(
            new CountryResource($country),
            'Country retrieved successfully'
        );
    }

    /**
     * Get states/provinces for a country.
     *
     * @param string $countryCode
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: StateResource[]
     *  }
     */
    public function getStates(string $countryCode): JsonResponse
    {
        $states = $this->worldService->getStates($countryCode);

        return response()->success(
            StateResource::collection($states),
            'States retrieved successfully'
        );
    }

    /**
     * Get cities for a state.
     *
     * @param string $countryCode
     * @param string $stateCode
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: CityResource[]
     *  }
     */
    public function getCities(string $countryCode, string $stateCode): JsonResponse
    {
        $cities = $this->worldService->getCities($countryCode, $stateCode);

        return response()->success(
            CityResource::collection($cities),
            'Cities retrieved successfully'
        );
    }

    /**
     * Get all currencies.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: CurrencyResource[]
     *  }
     */
    public function getCurrencies(): JsonResponse
    {
        $currencies = $this->worldService->getCurrencies();

        return response()->success(
            CurrencyResource::collection($currencies),
            'Currencies retrieved successfully'
        );
    }

    /**
     * Get a currency by code.
     *
     * @param string $code
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: CurrencyResource
     *  }
     */
    public function getCurrencyByCode(string $code): JsonResponse
    {
        $currency = $this->worldService->getCurrencyByCode($code);

        if (!$currency) {
            return response()->error('Currency not found', 404);
        }

        return response()->success(
            new CurrencyResource($currency),
            'Currency retrieved successfully'
        );
    }

    /**
     * Get all timezones.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: TimezoneResource[]
     *  }
     */
    public function getTimezones(): JsonResponse
    {
        $timezones = $this->worldService->getTimezones();

        return response()->success(
            TimezoneResource::collection($timezones),
            'Timezones retrieved successfully'
        );
    }

    /**
     * Get timezones for a country.
     *
     * @param string $countryCode
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: TimezoneResource[]
     *  }
     */
    public function getTimezonesByCountry(string $countryCode): JsonResponse
    {
        $timezones = $this->worldService->getTimezonesByCountry($countryCode);

        return response()->success(
            TimezoneResource::collection($timezones),
            'Timezones retrieved successfully'
        );
    }

    /**
     * Get all languages.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array
     *  }
     */
    public function getLanguages(): JsonResponse
    {
        $languages = $this->worldService->getLanguages();

        return response()->success(
            $languages,
            'Languages retrieved successfully'
        );
    }

    /**
     * Get a language by code.
     *
     * @param string $code
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array
     *  }
     */
    public function getLanguageByCode(string $code): JsonResponse
    {
        $language = $this->worldService->getLanguageByCode($code);

        if (!$language) {
            return response()->error('Language not found', 404);
        }

        return response()->success(
            $language,
            'Language retrieved successfully'
        );
    }

    /**
     * Get country phone codes.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array
     *  }
     */
    public function getPhoneCodes(): JsonResponse
    {
        $phoneCodes = $this->worldService->getPhoneCodes();

        return response()->success(
            $phoneCodes,
            'Phone codes retrieved successfully'
        );
    }

    /**
     * Get a phone code by country code.
     *
     * @param string $countryCode
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: string
     *  }
     */
    public function getPhoneCodeByCountry(string $countryCode): JsonResponse
    {
        $phoneCode = $this->worldService->getPhoneCodeByCountry($countryCode);

        if (!$phoneCode) {
            return response()->error('Phone code not found', 404);
        }

        return response()->success(
            $phoneCode,
            'Phone code retrieved successfully'
        );
    }

    /**
     * Clear world data cache.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function clearCache(): JsonResponse
    {
        $this->worldService->clearCache();

        return response()->success(
            null,
            'World data cache cleared successfully'
        );
    }
}
