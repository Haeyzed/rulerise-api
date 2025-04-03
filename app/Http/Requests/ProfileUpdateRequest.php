<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends BaseRequest
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
             * The profile picture of the user.
             *
             * @var file|null $profile_picture
             */
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}

