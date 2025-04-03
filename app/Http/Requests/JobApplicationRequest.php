<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatusEnum;
use Illuminate\Validation\Rule;

class JobApplicationRequest extends BaseRequest
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
             * The job ID associated with this application.
             *
             * @var int $job_id
             * @example 1
             */
            'job_id' => ['required', 'exists:jobs,id'],
            
            /**
             * The candidate ID associated with this application.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => ['required', 'exists:candidate_profiles,id'],
            
            /**
             * The resume ID associated with this application.
             *
             * @var int|null $resume_id
             * @example 1
             */
            'resume_id' => ['nullable', 'exists:candidate_resumes,id'],
            
            /**
             * The cover letter for the application.
             *
             * @var string|null $cover_letter
             * @example "I am writing to express my interest in the Software Engineer position..."
             */
            'cover_letter' => ['nullable', 'string'],
            
            /**
             * The status of the application.
             *
             * @var string $status
             * @example "pending"
             */
            'status' => ['nullable', 'string', Rule::in(ApplicationStatusEnum::values())],
            
            /**
             * The expected salary specified in the application.
             *
             * @var float|null $expected_salary
             * @example 90000
             */
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            
            /**
             * The currency of the expected salary.
             *
             * @var string|null $salary_currency
             * @example "USD"
             */
            'salary_currency' => ['nullable', 'string', 'max:3'],
            
            /**
             * The date the candidate is available to start.
             *
             * @var string|null $availability_date
             * @example "2023-03-15"
             */
            'availability_date' => ['nullable', 'date'],
            
            /**
             * Additional notes for the application.
             *
             * @var string|null $notes
             * @example "I am particularly interested in working with your AI team."
             */
            'notes' => ['nullable', 'string'],
        ];
    }
}

