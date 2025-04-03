<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateProfileResource extends JsonResource
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
             * The unique identifier for the candidate profile.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The user details associated with this profile.
             *
             * @var array|null $user
             */
            'user' => new UserResource($this->whenLoaded('user')),

            /**
             * The professional title of the candidate.
             *
             * @var string|null $title
             * @example "Senior Software Engineer"
             */
            'title' => $this->title,

            /**
             * The headline of the candidate.
             *
             * @var string|null $headline
             * @example "Innovative Tech Leader | Bridging Business and Technology"
             */
            'headline' => $this->headline,

            /**
             * The biography or summary of the candidate.
             *
             * @var string|null $bio
             * @example "Experienced software engineer with 8+ years in web development..."
             */
            'bio' => $this->bio,

            /**
             * The date of birth of the candidate.
             *
             * @var string|null $date_of_birth
             * @example "1990-01-15"
             */
            'date_of_birth' => $this->date_of_birth,

            /**
             * The age of the candidate calculated from date of birth.
             *
             * @var int|null $age
             * @example 33
             */
            'age' => $this->age,

            /**
             * The gender of the candidate.
             *
             * @var string|null $gender
             * @example "male"
             */
            'gender' => $this->gender,

            /**
             * The human-readable label for the gender.
             *
             * @var string|null $gender_label
             * @example "Male"
             */
            'gender_label' => $this->gender ? $this->gender->label() : null,

            /**
             * The years of professional experience.
             *
             * @var int|null $experience_years
             * @example 8
             */
            'experience_years' => $this->experience_years,

            /**
             * The current salary of the candidate.
             *
             * @var float|null $current_salary
             * @example 85000
             */
            'current_salary' => $this->current_salary,

            /**
             * The expected salary of the candidate.
             *
             * @var float|null $expected_salary
             * @example 95000
             */
            'expected_salary' => $this->expected_salary,

            /**
             * The currency of the salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => $this->salary_currency,

            /**
             * The education level details of the candidate.
             *
             * @var array|null $education_level
             */
            'education_level' => new EducationLevelResource($this->whenLoaded('educationLevel')),

            /**
             * The address of the candidate.
             *
             * @var string|null $address
             * @example "123 Main Street"
             */
            'address' => $this->address,

            /**
             * The city details of the candidate.
             *
             * @var array|null $city
             */
            'city' => new CityResource($this->whenLoaded('city')),

            /**
             * The state details of the candidate.
             *
             * @var array|null $state
             */
            'state' => new StateResource($this->whenLoaded('state')),

            /**
             * The country details of the candidate.
             *
             * @var array|null $country
             */
            'country' => new CountryResource($this->whenLoaded('country')),

            /**
             * The postal code of the candidate.
             *
             * @var string|null $postal_code
             * @example "94105"
             */
            'postal_code' => $this->postal_code,

            /**
             * The full location of the candidate (city, state, country).
             *
             * @var string $full_location
             * @example "San Francisco, California, United States"
             */
            'full_location' => $this->full_location,

            /**
             * Whether the candidate prefers remote work.
             *
             * @var bool $is_remote_preferred
             * @example true
             */
            'is_remote_preferred' => $this->is_remote_preferred,

            /**
             * Whether the candidate profile is public.
             *
             * @var bool $is_public
             * @example true
             */
            'is_public' => $this->is_public,

            /**
             * Whether the candidate is available for work.
             *
             * @var bool $is_available
             * @example true
             */
            'is_available' => $this->is_available,

            /**
             * Whether the candidate profile is featured.
             *
             * @var bool $is_featured
             * @example false
             */
            'is_featured' => $this->is_featured,

            /**
             * The number of views the profile has received.
             *
             * @var int $views_count
             * @example 150
             */
            'views_count' => $this->views_count,

            /**
             * The Facebook URL of the candidate.
             *
             * @var string|null $facebook_url
             * @example "https://facebook.com/johndoe"
             */
            'facebook_url' => $this->facebook_url,

            /**
             * The Twitter URL of the candidate.
             *
             * @var string|null $twitter_url
             * @example "https://twitter.com/johndoe"
             */
            'twitter_url' => $this->twitter_url,

            /**
             * The LinkedIn URL of the candidate.
             *
             * @var string|null $linkedin_url
             * @example "https://linkedin.com/in/johndoe"
             */
            'linkedin_url' => $this->linkedin_url,

            /**
             * The GitHub URL of the candidate.
             *
             * @var string|null $github_url
             * @example "https://github.com/johndoe"
             */
            'github_url' => $this->github_url,

            /**
             * The portfolio URL of the candidate.
             *
             * @var string|null $portfolio_url
             * @example "https://johndoe.com"
             */
            'portfolio_url' => $this->portfolio_url,

            /**
             * The skills of the candidate.
             *
             * @var array|null $skills
             */
            'skills' => CandidateSkillResource::collection($this->whenLoaded('skills')),

            /**
             * The education history of the candidate.
             *
             * @var array|null $educations
             */
            'educations' => CandidateEducationResource::collection($this->whenLoaded('educations')),

            /**
             * The work experience history of the candidate.
             *
             * @var array|null $experiences
             */
            'experiences' => CandidateExperienceResource::collection($this->whenLoaded('experiences')),

            /**
             * The projects of the candidate.
             *
             * @var array|null $projects
             */
            'projects' => CandidateProjectResource::collection($this->whenLoaded('projects')),

            /**
             * The certifications of the candidate.
             *
             * @var array|null $certifications
             */
            'certifications' => CandidateCertificationResource::collection($this->whenLoaded('certifications')),

            /**
             * The languages known by the candidate.
             *
             * @var array|null $languages
             */
            'languages' => CandidateLanguageResource::collection($this->whenLoaded('languages')),

            /**
             * The resumes of the candidate.
             *
             * @var array|null $resumes
             */
            'resumes' => CandidateResumeResource::collection($this->whenLoaded('resumes')),

            /**
             * The primary resume of the candidate.
             *
             * @var array|null $primary_resume
             */
            'primary_resume' => $this->when($this->primaryResume(), function() {
                return new CandidateResumeResource($this->primaryResume());
            }),

            /**
             * The job applications of the candidate.
             *
             * @var array|null $applications
             */
            'applications' => JobApplicationResource::collection($this->whenLoaded('applications')),

            /**
             * The timestamp when the profile was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the profile was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

