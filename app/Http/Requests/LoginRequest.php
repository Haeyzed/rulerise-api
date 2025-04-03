<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
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
        return [
            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => ['required', 'string', 'email'],
            
            /**
             * The password for the user account.
             *
             * @var string $password
             * @example "SecureP@ssw0rd"
             */
            'password' => ['required', 'string'],
            
            /**
             * Whether to remember the user's login session.
             *
             * @var bool|null $remember
             * @example true
             */
            'remember' => ['nullable', 'boolean'],
        ];
    }
}

