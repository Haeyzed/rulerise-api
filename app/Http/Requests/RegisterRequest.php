<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use App\Http\Requests\BaseRequest;
use App\Models\Country;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class RegisterRequest extends BaseRequest
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
        $baseRules = [
            /**
             * The first name of the user.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => ['required', 'string', 'max:100'],

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => ['required', 'string', 'max:100'],

            /**
             * The phone number of the user.
             *
             * @var string|null $phone
             * @example "+1234567890"
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
             * @example "john.doe@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            /**
             * The password for the user account.
             *
             * @var string $password
             * @example "SecureP@ssw0rd"
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
             * @example "SecureP@ssw0rd"
             */
            'password_confirmation' => ['required', 'string'],

            /**
             * The role of the user (candidate, employer).
             *
             * @var string $role
             * @example "candidate"
             */
            'role' => ['required', 'string', Rule::in([UserRoleEnum::CANDIDATE->value, UserRoleEnum::EMPLOYER->value])],

            /**
             * The professional title of the candidate.
             *
             * @var string|null $title
             * @example "Senior Software Engineer"
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
             * @example "Experienced software engineer with 5+ years in web development."
             */
            'bio' => ['nullable', 'string'],

            /**
             * The headline of the candidate.
             *
             * @var string|null $headline
             * @example "Full Stack Developer | React | Laravel | AWS"
             */
            'headline' => ['nullable', 'string', 'max:255'],

            /**
             * The education level ID of the candidate.
             *
             * @var int|null $education_level_id
             * @example 3
             */
            'education_level_id' => ['nullable', 'exists:education_levels,id'],

            /**
             * The name of the company (for employer registration).
             *
             * @var string|null $company_name
             * @example "Acme Corporation"
             */
            'company_name' => ['nullable', 'string', 'max:255'],

            /**
             * The industry of the company (for employer registration).
             *
             * @var string|null $company_industry
             * @example "Technology"
             */
            'company_industry' => ['nullable', 'string', 'max:255'],

            /**
             * The company size ID (for employer registration).
             *
             * @var int|null $company_size_id
             * @example 3
             */
            'company_size_id' => ['nullable', 'exists:company_sizes,id'],

            /**
             * The year the company was founded (for employer registration).
             *
             * @var int|null $company_founded_year
             * @example 2010
             */
            'company_founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],

            /**
             * The company email (for employer registration).
             *
             * @var string|null $company_email
             * @example "info@acme.com"
             */
            'company_email' => ['nullable', 'string', 'email', 'max:255'],

            /**
             * The company phone (for employer registration).
             *
             * @var string|null $company_phone
             * @example "+1234567890"
             */
            'company_phone' => ['nullable', new PhoneRule($countryCodes)],

            /**
             * The country code for the company phone number.
             *
             * @var string|null $company_phone_country
             * @example "US"
             */
            'company_phone_country' => ['required_with:company_phone', 'string', Rule::in($countryCodes)],

            /**
             * The company website (for employer registration).
             *
             * @var string|null $company_website
             * @example "https://acme.com"
             */
            'company_website' => ['nullable', 'string', 'url', 'max:255'],

            /**
             * The company description (for employer registration).
             *
             * @var string|null $company_description
             * @example "Acme Corporation is a leading technology company..."
             */
            'company_description' => ['nullable', 'string'],

            /**
             * The company address (for employer registration).
             *
             * @var string|null $company_address
             * @example "123 Main St, Suite 100"
             */
            'company_address' => ['nullable', 'string', 'max:255'],

            /**
             * The company city ID (for employer registration).
             *
             * @var int|null $company_city_id
             * @example 1
             */
            'company_city_id' => ['nullable', 'exists:cities,id'],

            /**
             * The company state ID (for employer registration).
             *
             * @var int|null $company_state_id
             * @example 1
             */
            'company_state_id' => ['nullable', 'exists:states,id'],

            /**
             * The company country ID (for employer registration).
             *
             * @var int|null $company_country_id
             * @example 1
             */
            'company_country_id' => ['nullable', 'exists:countries,id'],

            /**
             * The company logo (for employer registration).
             *
             * @var \Illuminate\Http\UploadedFile|null $company_logo
             */
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        // Apply conditional validation based on role
        if ($this->input('role') === UserRoleEnum::EMPLOYER->value) {
            // Make company_name required for employers
            $baseRules['company_name'] = ['required', 'string', 'max:255'];
        }

        return $baseRules;
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // For candidate role, ignore employer fields
            if ($this->input('role') === UserRoleEnum::CANDIDATE->value) {
                // No additional validation needed
            }

            // For employer role, ensure company_name is provided
            if ($this->input('role') === UserRoleEnum::EMPLOYER->value) {
                if (empty($this->input('company_name'))) {
                    $validator->errors()->add('company_name', 'Company name is required when registering as an employer.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Please select a role (candidate or employer).',
            'role.in' => 'The selected role must be either candidate or employer.',
            'company_name.required' => 'Company name is required when registering as an employer.',
            'company_logo.image' => 'The company logo must be an image file.',
            'company_logo.mimes' => 'The company logo must be a file of type: jpeg, png, jpg, gif.',
            'company_logo.max' => 'The company logo may not be greater than 2MB.',
            'company_founded_year.integer' => 'The company founded year must be a valid year.',
            'company_founded_year.min' => 'The company founded year must be after 1800.',
            'company_founded_year.max' => 'The company founded year cannot be in the future.',

            // Phone validation messages
            'phone.phone' => 'The phone number is invalid. Please enter a valid phone number with country code.',
            'phone_country.required_with' => 'Please select a country for your phone number.',
            'phone_country.in' => 'The selected phone country is invalid.',

            // Company phone validation messages
            'company_phone.phone' => 'The company phone number is invalid. Please enter a valid phone number with country code.',
            'company_phone_country.required_with' => 'Please select a country for the company phone number.',
            'company_phone_country.in' => 'The selected company phone country is invalid.',
        ];
    }
}
