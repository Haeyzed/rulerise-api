<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
             * The unique identifier for the job.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The company that posted the job.
             *
             * @var array|null $company
             */
            'company' => new CompanyResource($this->whenLoaded('company')),

            /**
             * The category of the job.
             *
             * @var array|null $category
             */
            'category' => new JobCategoryResource($this->whenLoaded('category')),

            /**
             * The type of the job (full-time, part-time, etc.).
             *
             * @var array|null $job_type
             */
            'job_type' => new JobTypeResource($this->whenLoaded('jobType')),

            /**
             * The experience level required for the job.
             *
             * @var array|null $experience_level
             */
            'experience_level' => new ExperienceLevelResource($this->whenLoaded('experienceLevel')),

            /**
             * The education level required for the job.
             *
             * @var array|null $education_level
             */
            'education_level' => new EducationLevelResource($this->whenLoaded('educationLevel')),

            /**
             * The title of the job.
             *
             * @var string $title
             * @example "Senior Software Engineer"
             */
            'title' => $this->title,

            /**
             * The URL-friendly slug of the job.
             *
             * @var string $slug
             * @example "senior-software-engineer-123"
             */
            'slug' => $this->slug,

            /**
             * The description of the job.
             *
             * @var string $description
             * @example "We are looking for a Senior Software Engineer to join our team..."
             */
            'description' => $this->description,

            /**
             * The responsibilities of the job.
             *
             * @var string|null $responsibilities
             * @example "Design and implement high-quality code..."
             */
            'responsibilities' => $this->responsibilities,

            /**
             * The requirements of the job.
             *
             * @var string|null $requirements
             * @example "5+ years of experience in software development..."
             */
            'requirements' => $this->requirements,

            /**
             * The benefits offered with the job.
             *
             * @var string|null $benefits
             * @example "Health insurance, 401k matching, flexible work hours..."
             */
            'benefits' => $this->benefits,

            /**
             * The minimum salary for the job.
             *
             * @var float|null $min_salary
             * @example 80000
             */
            'min_salary' => $this->min_salary,

            /**
             * The maximum salary for the job.
             *
             * @var float|null $max_salary
             * @example 120000
             */
            'max_salary' => $this->max_salary,

            /**
             * The currency of the salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => $this->salary_currency,

            /**
             * The period of the salary (hourly, monthly, yearly).
             *
             * @var string|null $salary_period
             * @example "yearly"
             */
            'salary_period' => $this->salary_period,

            /**
             * The human-readable label for the salary period.
             *
             * @var string|null $salary_period_label
             * @example "Per Year"
             */
            'salary_period_label' => $this->salary_period ? $this->salary_period->label() : null,

            /**
             * The formatted salary range.
             *
             * @var string|null $formatted_salary_range
             * @example "USD 80000 - 120000 Per Year"
             */
            'formatted_salary_range' => $this->formatted_salary_range,

            /**
             * Whether the salary is visible to applicants.
             *
             * @var bool $is_salary_visible
             * @example true
             */
            'is_salary_visible' => $this->is_salary_visible,

            /**
             * The location of the job.
             *
             * @var string|null $location
             * @example "San Francisco Bay Area"
             */
            'location' => $this->location,

            /**
             * The address of the job.
             *
             * @var string|null $address
             * @example "123 Main Street"
             */
            'address' => $this->address,

            /**
             * The city ID of the job.
             *
             * @var int|null $city_id
             * @example 1
             */
            'city_id' => $this->city_id,

            /**
             * The state ID of the job.
             *
             * @var int|null $state_id
             * @example 1
             */
            'state_id' => $this->state_id,

            /**
             * The country ID of the job.
             *
             * @var int|null $country_id
             * @example 1
             */
            'country_id' => $this->country_id,

            /**
             * The postal code of the job.
             *
             * @var string|null $postal_code
             * @example "94105"
             */
            'postal_code' => $this->postal_code,

            /**
             * Whether the job is remote.
             *
             * @var bool $is_remote
             * @example true
             */
            'is_remote' => $this->is_remote,

            /**
             * The full location of the job (city, state, country or Remote).
             *
             * @var string $full_location
             * @example "San Francisco, California, United States"
             */
            'full_location' => $this->full_location,

            /**
             * The application deadline for the job.
             *
             * @var string|null $application_deadline
             * @example "2023-12-31"
             */
            'application_deadline' => $this->application_deadline,

            /**
             * Whether the application deadline has passed.
             *
             * @var bool $is_deadline_passed
             * @example false
             */
            'is_deadline_passed' => $this->is_deadline_passed,

            /**
             * The number of days until the application deadline.
             *
             * @var int|null $days_until_deadline
             * @example 30
             */
            'days_until_deadline' => $this->days_until_deadline,

            /**
             * Whether the job is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * Whether the job is featured.
             *
             * @var bool $is_featured
             * @example false
             */
            'is_featured' => $this->is_featured,

            /**
             * Whether the job is marked as urgent.
             *
             * @var bool $is_urgent
             * @example false
             */
            'is_urgent' => $this->is_urgent,

            /**
             * Whether the job was posted recently (within 7 days).
             *
             * @var bool $is_recently_posted
             * @example true
             */
            'is_recently_posted' => $this->is_recently_posted,

            /**
             * The number of vacancies for the job.
             *
             * @var int|null $vacancies
             * @example 2
             */
            'vacancies' => $this->vacancies,

            /**
             * The number of views the job has received.
             *
             * @var int $views_count
             * @example 150
             */
            'views_count' => $this->views_count,

            /**
             * The number of applications the job has received.
             *
             * @var int $applications_count
             * @example 25
             */
            'applications_count' => $this->applications_count,

            /**
             * The external URL to apply for the job.
             *
             * @var string|null $external_apply_url
             * @example "https://example.com/careers/apply/123"
             */
            'external_apply_url' => $this->external_apply_url,

            /**
             * The skills required for the job.
             *
             * @var array|null $skills
             */
            'skills' => SkillResource::collection($this->whenLoaded('skills')),

            /**
             * The timestamp when the job was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the job was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the job was deleted (soft delete).
             *
             * @var string|null $deleted_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'deleted_at' => $this->deleted_at,
        ];
    }
}

