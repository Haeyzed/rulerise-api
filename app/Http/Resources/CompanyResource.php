<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
             * The unique identifier for the company.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the company.
             *
             * @var string $name
             * @example "Acme Corporation"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the company.
             *
             * @var string $slug
             * @example "acme-corporation"
             */
            'slug' => $this->slug,

            /**
             * The logo file path of the company.
             *
             * @var string|null $logo
             * @example "companies/logo_1234567890.jpg"
             */
            'logo' => $this->logo,

            /**
             * The full URL to the company's logo.
             *
             * @var string|null $logo_url
             * @example "https://example.com/storage/companies/logo_1234567890.jpg"
             */
            'logo_url' => $this->logo_url,

            /**
             * The banner file path of the company.
             *
             * @var string|null $banner
             * @example "companies/banner_1234567890.jpg"
             */
            'banner' => $this->banner,

            /**
             * The full URL to the company's banner.
             *
             * @var string|null $banner_url
             * @example "https://example.com/storage/companies/banner_1234567890.jpg"
             */
            'banner_url' => $this->banner_url,

            /**
             * The website URL of the company.
             *
             * @var string|null $website
             * @example "https://acme.com"
             */
            'website' => $this->website,

            /**
             * The description of the company.
             *
             * @var string|null $description
             * @example "Acme Corporation is a leading provider of innovative solutions..."
             */
            'description' => $this->description,

            /**
             * The industry of the company.
             *
             * @var string|null $industry
             * @example "Technology"
             */
            'industry' => $this->industry,

            /**
             * The company size details.
             *
             * @var array|null $company_size
             */
            'company_size' => new CompanySizeResource($this->whenLoaded('companySize')),

            /**
             * The year the company was founded.
             *
             * @var int|null $founded_year
             * @example 2005
             */
            'founded_year' => $this->founded_year,

            /**
             * The address of the company.
             *
             * @var string|null $address
             * @example "123 Main Street"
             */
            'address' => $this->address,

            /**
             * The city details of the company.
             *
             * @var array|null $city
             */
            'city' => new CityResource($this->whenLoaded('city')),

            /**
             * The state details of the company.
             *
             * @var array|null $state
             */
            'state' => new StateResource($this->whenLoaded('state')),

            /**
             * The country details of the company.
             *
             * @var array|null $country
             */
            'country' => new CountryResource($this->whenLoaded('country')),

            /**
             * The postal code of the company.
             *
             * @var string|null $postal_code
             * @example "94105"
             */
            'postal_code' => $this->postal_code,

            /**
             * The full location of the company (city, state, country).
             *
             * @var string $full_location
             * @example "San Francisco, California, United States"
             */
            'full_location' => $this->full_location,

            /**
             * Whether the company is verified.
             *
             * @var bool $is_verified
             * @example true
             */
            'is_verified' => $this->is_verified,

            /**
             * Whether the company is featured.
             *
             * @var bool $is_featured
             * @example false
             */
            'is_featured' => $this->is_featured,

            /**
             * The Facebook URL of the company.
             *
             * @var string|null $facebook_url
             * @example "https://facebook.com/acmecorp"
             */
            'facebook_url' => $this->facebook_url,

            /**
             * The Twitter URL of the company.
             *
             * @var string|null $twitter_url
             * @example "https://twitter.com/acmecorp"
             */
            'twitter_url' => $this->twitter_url,

            /**
             * The LinkedIn URL of the company.
             *
             * @var string|null $linkedin_url
             * @example "https://linkedin.com/company/acmecorp"
             */
            'linkedin_url' => $this->linkedin_url,

            /**
             * The Instagram URL of the company.
             *
             * @var string|null $instagram_url
             * @example "https://instagram.com/acmecorp"
             */
            'instagram_url' => $this->instagram_url,

            /**
             * The user who owns the company.
             *
             * @var array|null $user
             */
            'user' => new UserResource($this->whenLoaded('user')),

            /**
             * The jobs posted by the company.
             *
             * @var array|null $jobs
             */
            'jobs' => JobResource::collection($this->whenLoaded('jobs')),

            /**
             * The active subscription of the company.
             *
             * @var array|null $active_subscription
             */
            'active_subscription' => $this->when($this->activeSubscription(), function() {
                return new CompanySubscriptionResource($this->activeSubscription());
            }),

            /**
             * The timestamp when the company was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the company was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the company was deleted (soft delete).
             *
             * @var string|null $deleted_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}

