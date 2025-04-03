<?php

namespace App\Http\Requests;

use App\Enums\ProficiencyLevelEnum;
use App\Enums\UserRoleEnum;
use App\Http\Requests\BaseRequest;
use App\Models\Country;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class CandidateRegisterRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get all country ISO codes from the database
        $countryCodes = Country::query()->where('status', 1)->pluck('iso2')->toArray();

        return [
            /**
             * The first name of the user.
             *
             * @var string $first_name
             * @example "Michael"
             */
            'first_name' => ['required', 'string', 'max:100'],

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Johnson"
             */
            'last_name' => ['required', 'string', 'max:100'],

            /**
             * The phone number of the user.
             *
             * @var string|null $phone
             * @example "+14155552671"
             */
            'phone' => ['nullable', new PhoneRule($countryCodes)],

            /**
             * The country code for the phone number.
             *
             * @var string|null $phone_country
             * @example "US"
             */
            'phone_country' => ['required_with:phone', 'string', Rule::in($countryCodes)],

            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "michael.johnson@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            /**
             * The password for the user account.
             *
             * @var string $password
             * @example "StrongP@ss123"
             */
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ],

            /**
             * The password confirmation for the user account.
             *
             * @var string $password_confirmation
             * @example "StrongP@ss123"
             */
            'password_confirmation' => ['required', 'string'],

            /**
             * The professional title of the candidate.
             *
             * @var string|null $title
             * @example "Full Stack Developer"
             */
            'title' => ['nullable', 'string', 'max:255'],

            /**
             * Whether the candidate profile is public.
             *
             * @var bool|null $is_public
             * @example true
             */
            'is_public' => ['nullable', 'boolean'],

            /**
             * Whether the candidate is available for work.
             *
             * @var bool|null $is_available
             * @example true
             */
            'is_available' => ['nullable', 'boolean'],

            /**
             * The bio of the candidate.
             *
             * @var string|null $bio
             * @example "Full Stack Developer with 5+ years of experience in building web applications using React, Node.js, and Laravel. Passionate about clean code and user experience."
             */
            'bio' => ['nullable', 'string'],

            /**
             * The headline of the candidate.
             *
             * @var string|null $headline
             * @example "Full Stack Developer | React | Node.js | Laravel"
             */
            'headline' => ['nullable', 'string', 'max:255'],

            /**
             * The education level ID of the candidate.
             *
             * @var int|null $education_level_id
             * @example 4
             */
            'education_level_id' => ['nullable', 'exists:education_levels,id'],

            /**
             * The skills of the candidate.
             *
             * @var array|null $skills
             */
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['nullable', 'exists:skills,id'],
            'skills.*.level' => ['nullable', 'integer', 'min:1', 'max:5'],

            /**
             * The proficiency level for each skill.
             *
             * @var string|null $proficiency_level
             */
            'skills.*.proficiency_level' => ['nullable', 'string', Rule::in(ProficiencyLevelEnum::values())],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Phone validation messages
            'phone.phone' => 'The phone number is invalid. Please enter a valid phone number with country code.',
            'phone_country.required_with' => 'Please select a country for your phone number.',
            'phone_country.in' => 'The selected phone country is invalid.',

            // Skills validation messages
            'skills.array' => 'Skills must be provided as an array.',
            'skills.*.id.exists' => 'One or more selected skills do not exist.',
            'skills.*.level.integer' => 'Skill level must be an integer.',
            'skills.*.level.min' => 'Skill level must be at least 1.',
            'skills.*.level.max' => 'Skill level cannot be greater than 5.',
            'skills.*.proficiency_level.in' => 'The proficiency level must be one of the following: beginner, intermediate, advanced, expert.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => UserRoleEnum::CANDIDATE->value,
        ]);
    }
}
