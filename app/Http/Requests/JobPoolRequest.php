<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobPoolRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller and policies
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The name of the job pool.
             *
             * @var string $name
             * @example "Software Engineers Pool 2023"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * The detailed description of the job pool.
             *
             * @var string $description
             * @example "A talent pool for software engineers with expertise in cloud technologies..."
             */
            'description' => ['required', 'string'],

            /**
             * The ID of the company that owns this job pool.
             *
             * @var int $company_id
             * @example 1
             */
            'company_id' => ['required', 'exists:companies,id'],

            /**
             * The ID of the job category for this pool.
             *
             * @var int $job_category_id
             * @example 2
             */
            'job_category_id' => ['required', 'exists:job_categories,id'],

            /**
             * The ID of the experience level required for this pool.
             *
             * @var int $experience_level_id
             * @example 3
             */
            'experience_level_id' => ['required', 'exists:experience_levels,id'],

            /**
             * The ID of the education level required for this pool.
             *
             * @var int|null $education_level_id
             * @example 4
             */
            'education_level_id' => ['nullable', 'exists:education_levels,id'],

            /**
             * The skills required for this job pool.
             *
             * @var array $skills
             * @example [{"skill_id": 1, "is_required": true}, {"skill_id": 5, "is_required": false}]
             */
            'skills' => ['required', 'array', 'min:1'],

            /**
             * The ID of an individual skill.
             *
             * @var int $skills.*.skill_id
             * @example 1
             */
            'skills.*.skill_id' => ['required', 'exists:skills,id'],

            /**
             * Whether the skill is required or optional.
             *
             * @var bool|null $skills.*.is_required
             * @example true
             */
            'skills.*.is_required' => ['nullable', 'boolean'],

            /**
             * Whether the job pool is active.
             *
             * @var bool|null $is_active
             * @example true
             */
            'is_active' => ['nullable', 'boolean'],

            /**
             * The maximum number of candidates allowed in this pool.
             *
             * @var int|null $max_candidates
             * @example 50
             */
            'max_candidates' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

