<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use App\Http\Requests\BaseRequest;
use App\Models\Country;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class EmployerRegisterRequest extends BaseRequest
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
             * @example "Sarah"
             */
            'first_name' => ['required', 'string', 'max:100'],

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Williams"
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
             * @example "sarah.williams@techcorp.com"
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
             * The name of the company.
             *
             * @var string $company_name
             * @example "TechCorp Solutions"
             */
            'company_name' => ['required', 'string', 'max:255'],

            /**
             * The industry of the company.
             *
             * @var string|null $company_industry
             * @example "Information Technology"
             */
            'company_industry' => ['nullable', 'string', 'max:255'],

            /**
             * The company size ID.
             *
             * @var int|null $company_size_id
             * @example 3
             */
            'company_size_id' => ['nullable', 'exists:company_sizes,id'],

            /**
             * The year the company was founded.
             *
             * @var int|null $company_founded_year
             * @example 2015
             */
            'company_founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],

            /**
             * The company email.
             *
             * @var string|null $company_email
             * @example "info@techcorp.com"
             */
            'company_email' => ['nullable', 'string', 'email', 'max:255'],

            /**
             * The company phone.
             *
             * @var string|null $company_phone
             * @example "+14155552672"
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
             * The company website.
             *
             * @var string|null $company_website
             * @example "https://techcorp.com"
             */
            'company_website' => ['nullable', 'string', 'url', 'max:255'],

            /**
             * The company description.
             *
             * @var string|null $company_description
             * @example "TechCorp Solutions is a leading provider of innovative software solutions for businesses of all sizes. We specialize in custom software development, cloud solutions, and digital transformation."
             */
            'company_description' => ['nullable', 'string'],

            /**
             * The company address.
             *
             * @var string|null $company_address
             * @example "123 Tech Street, Suite 500"
             */
            'company_address' => ['nullable', 'string', 'max:255'],

            /**
             * The company city ID.
             *
             * @var int|null $company_city_id
             * @example 1
             */
            'company_city_id' => ['nullable', 'exists:cities,id'],

            /**
             * The company state ID.
             *
             * @var int|null $company_state_id
             * @example 5
             */
            'company_state_id' => ['nullable', 'exists:states,id'],

            /**
             * The company country ID.
             *
             * @var int|null $company_country_id
             * @example 1
             */
            'company_country_id' => ['nullable', 'exists:countries,id'],

            /**
             * The company postal code.
             *
             * @var string|null $company_postal_code
             * @example "94105"
             */
            'company_postal_code' => ['nullable', 'string', 'max:20'],

            /**
             * The company logo.
             *
             * @var \Illuminate\Http\UploadedFile|null $company_logo
             */
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => UserRoleEnum::EMPLOYER->value,
        ]);
    }
}
