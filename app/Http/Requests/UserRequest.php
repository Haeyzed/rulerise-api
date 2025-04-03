<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            
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
            'phone' => ['nullable', 'string', 'max:20'],
            
            /**
             * The role of the user.
             *
             * @var string|null $role
             * @example "candidate"
             */
            'role' => ['nullable', 'string', Rule::in(UserRoleEnum::values())],
            
            /**
             * Whether the user is active.
             *
             * @var bool|null $is_active
             * @example true
             */
            'is_active' => ['nullable', 'boolean'],
            
            /**
             * The profile picture of the user.
             *
             * @var file|null $profile_picture
             */
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        // Add password rules for create request
        if (!$this->user) {
            $rules['password'] = [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ];
        } else {
            $rules['password'] = [
                'nullable',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ];
        }

        return $rules;
    }
}

