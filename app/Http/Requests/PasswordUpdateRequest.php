<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends BaseRequest
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
             * The current password of the user.
             *
             * @var string $current_password
             * @example "CurrentP@ssw0rd"
             */
            'current_password' => ['required', 'string'],
            
            /**
             * The new password for the user account.
             *
             * @var string $password
             * @example "NewSecureP@ssw0rd"
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
                'different:current_password',
            ],
        ];
    }
}

