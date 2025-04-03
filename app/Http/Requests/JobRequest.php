<?php

namespace App\Http\Requests;

use App\Enums\SalaryPeriodEnum;
use Illuminate\Validation\Rule;

class JobRequest extends BaseRequest
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
             * The company ID that the job belongs to.
             *
             * @var int $company_id
             * @example 1
             */
            'company_id' => ['required', 'exists:companies,id'],
            
            /**
             * The job category ID.
             *
             * @var int $category_id
             * @example 3
             */
            'category_id' => ['required', 'exists:job_categories,id'],
            
            /**
             * The job type ID (full-time, part-time, etc.).
             *
             * @var int $job_type_id
             * @example 1
             */
            'job_type_id' => ['required', 'exists:job_types,id'],
            
            /**
             * The experience level ID required for the job.
             *
             * @var int $experience_level_id
             * @example 2
             */
            'experience_level_id' => ['required', 'exists:experience_levels,id'],
            
            /**
             * The education level ID required for the job.
             *
             * @var int|null $education_level_id
             * @example 3
             */
            'education_level_id' => ['nullable', 'exists:education_levels,id'],
            
            /**
             * The title of the job.
             *
             * @var string $title
             * @example "Senior Software Engineer"
             */
            'title' => ['required', 'string', 'max:255'],
            
            /**
             * The detailed description of the job.
             *
             * @var string $description
             * @example "We are looking for a Senior Software Engineer to join our team..."
             */
            'description' => ['required', 'string'],
            
            /**
             * The responsibilities of the job.
             *
             * @var string|null $responsibilities
             * @example "Design and implement high-quality code..."
             */
            'responsibilities' => ['nullable', 'string'],
            
            /**
             * The requirements for the job.
             *
             * @var string|null $requirements
             * @example "5+ years of experience in software development..."
             */
            'requirements' => ['nullable', 'string'],
            
            /**
             * The benefits offered with the job.
             *
             * @var string|null $benefits
             * @example "Health insurance, 401k matching, flexible work hours..."
             */
            'benefits' => ['nullable', 'string'],
            
            /**
             * The minimum salary for the job.
             *
             * @var float|null $min_salary
             * @example 80000
             */
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            
            /**
             * The maximum salary for the job.
             *
             * @var float|null $max_salary
             * @example 120000
             */
            'max_salary' => ['nullable', 'numeric', 'min:0', 'gte:min_salary'],
            
            /**
             * The currency of the salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => ['nullable', 'string', 'max:3'],
            
            /**
             * The period of the salary (hourly, monthly, yearly).
             *
             * @var string|null $salary_period
             * @example "yearly"
             */
            'salary_period' => ['nullable', 'string', Rule::in(SalaryPeriodEnum::values())],
            
            /**
             * Whether the salary is visible to applicants.
             *
             * @var bool $is_salary_visible
             * @example true
             */
            'is_salary_visible' => ['nullable', 'boolean'],
            
            /**
             * The location of the job.
             *
             * @var string|null $location
             * @example "San Francisco Bay Area"
             */
            'location' => ['nullable', 'string', 'max:255'],
            
            /**
             * The address of the job.
             *
             * @var string|null $address
             * @example "123 Main Street"
             */
            'address' => ['nullable', 'string', 'max:255'],
            
            /**
             * The city of the job.
             *
             * @var string|null $city
             * @example "San Francisco"
             */
            'city' => ['nullable', 'string', 'max:100'],
            
            /**
             * The state of the job.
             *
             * @var string|null $state
             * @example "California"
             */
            'state' => ['nullable', 'string', 'max:100'],
            
            /**
             * The country of the job.
             *
             * @var string $country
             * @example "United States"
             */
            'country' => ['required', 'string', 'max:100'],
            
            /**
             * The postal code of the job.
             *
             * @var string|null $postal_code
             * @example "94105"
             */
            'postal_code' => ['nullable', 'string', 'max:20'],
            
            /**
             * Whether the job is remote.
             *
             * @var bool $is_remote
             * @example true
             */
            'is_remote' => ['nullable', 'boolean'],
            
            /**
             * The application deadline for the job.
             *
             * @var string|null $application_deadline
             * @example "2023-12-31"
             */
            'application_deadline' => ['nullable', 'date'],
            
            /**
             * Whether the job is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => ['nullable', 'boolean'],
            
            /**
             * Whether the job is featured.
             *
             * @var bool $is_featured
             * @example false
             */
            'is_featured' => ['nullable', 'boolean'],
            
            /**
             * Whether the job is marked as urgent.
             *
             * @var bool $is_urgent
             * @example false
             */
            'is_urgent' => ['nullable', 'boolean'],
            
            /**
             * The number of vacancies for the job.
             *
             * @var int|null $vacancies
             * @example 2
             */
            'vacancies' => ['nullable', 'integer', 'min:1'],
            
            /**
             * The external URL to apply for the job.
             *
             * @var string|null $external_apply_url
             * @example "https://example.com/careers/apply/123"
             */
            'external_apply_url' => ['nullable', 'url', 'max:255'],
            
            /**
             * The skill IDs required for the job.
             *
             * @var array|null $skill_ids
             * @example [1, 4, 7]
             */
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['exists:skills,id'],
        ];
    }
}

