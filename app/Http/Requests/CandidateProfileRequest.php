<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use Illuminate\Validation\Rule;

class CandidateProfileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The user ID associated with this profile.
             *
             * @var int $user_id
             * @example 1
             */
            'user_id' => ['required', 'exists:users,id'],
            
            /**
             * The professional title of the candidate.
             *
             * @var string|null $title
             * @example "Senior Software Engineer"
             */
            'title' => ['nullable', 'string', 'max:255'],
            
            /**
             * The biography or summary of the candidate.
             *
             * @var string|null $bio
             * @example "Experienced software engineer with 8+ years in web development..."
             */
            'bio' => ['nullable', 'string'],
            
            /**
             * The date of birth of the candidate.
             *
             * @var string|null $date_of_birth
             * @example "1990-01-15"
             */
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            
            /**
             * The gender of the candidate.
             *
             * @var string|null $gender
             * @example "male"
             */
            'gender' => ['nullable', 'string', Rule::in(GenderEnum::values())],
            
            /**
             * The years of professional experience.
             *
             * @var int|null $experience_years
             * @example 8
             */
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            
            /**
             * The current salary of the candidate.
             *
             * @var float|null $current_salary
             * @example 85000
             */
            'current_salary' => ['nullable', 'numeric', 'min:0'],
            
            /**
             * The expected salary of the candidate.
             *
             * @var float|null $expected_salary
             * @example 95000
             */
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            
            /**
             * The currency of the salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => ['nullable', 'string', 'max:3'],
            
            /**
             * The education level ID of the candidate.
             *
             * @var int|null $education_level_id
             * @example 3
             */
            'education_level_id' => ['nullable', 'exists:education_levels,id'],
            
            /**
             * The address of the candidate.
             *
             * @var string|null $address
             * @example "123 Main Street"
             */
            'address' => ['nullable', 'string', 'max:255'],
            
            /**
             * The city ID of the candidate.
             *
             * @var int|null $city_id
             * @example 1
             */
            'city_id' => ['nullable', 'exists:cities,id'],
            
            /**
             * The state ID of the candidate.
             *
             * @var int|null $state_id
             * @example 1
             */
            'state_id' => ['nullable', 'exists:states,id'],
            
            /**
             * The country ID of the candidate.
             *
             * @var int|null $country_id
             * @example 1
             */
            'country_id' => ['nullable', 'exists:countries,id'],
            
            /**
             * The postal code of the candidate.
             *
             * @var string|null $postal_code
             * @example "94105"
             */
            'postal_code' => ['nullable', 'string', 'max:20'],
            
            /**
             * Whether the candidate prefers remote work.
             *
             * @var bool $is_remote_preferred
             * @example true
             */
            'is_remote_preferred' => ['nullable', 'boolean'],
            
            /**
             * Whether the candidate profile is public.
             *
             * @var bool $is_public
             * @example true
             */
            'is_public' => ['nullable', 'boolean'],
            
            /**
             * Whether the candidate is available for work.
             *
             * @var bool $is_available
             * @example true
             */
            'is_available' => ['nullable', 'boolean'],
            
            /**
             * Whether the candidate profile is featured.
             *
             * @var bool $is_featured
             * @example false
             */
            'is_featured' => ['nullable', 'boolean'],
            
            /**
             * The Facebook URL of the candidate.
             *
             * @var string|null $facebook_url
             * @example "https://facebook.com/johndoe"
             */
            'facebook_url' => ['nullable', 'url', 'max:255'],
            
            /**
             * The Twitter URL of the candidate.
             *
             * @var string|null $twitter_url
             * @example "https://twitter.com/johndoe"
             */
            'twitter_url' => ['nullable', 'url', 'max:255'],
            
            /**
             * The LinkedIn URL of the candidate.
             *
             * @var string|null $linkedin_url
             * @example "https://linkedin.com/in/johndoe"
             */
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            
            /**
             * The GitHub URL of the candidate.
             *
             * @var string|null $github_url
             * @example "https://github.com/johndoe"
             */
            'github_url' => ['nullable', 'url', 'max:255'],
            
            /**
             * The portfolio URL of the candidate.
             *
             * @var string|null $portfolio_url
             * @example "https://johndoe.com"
             */
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}

