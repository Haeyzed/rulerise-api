<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends BaseRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'company_size_id' => ['required', 'exists:company_sizes,id'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'industry' => ['nullable', 'string', 'max:255'],
            'social_media' => ['nullable', 'array'],
            'social_media.linkedin' => ['nullable', 'url', 'max:255'],
            'social_media.twitter' => ['nullable', 'url', 'max:255'],
            'social_media.facebook' => ['nullable', 'url', 'max:255'],
            'social_media.instagram' => ['nullable', 'url', 'max:255'],
        ];

        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                if (is_array($rule) && in_array('required', $rule)) {
                    return array_diff($rule, ['required']);
                }
                return $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->translate('company.name'),
            'description' => $this->translate('company.description'),
            'logo' => $this->translate('company.logo'),
            'website' => $this->translate('company.website'),
            'email' => $this->translate('company.email'),
            'phone' => $this->translate('company.phone'),
            'address' => $this->translate('company.address'),
            'city_id' => $this->translate('company.city'),
            'state_id' => $this->translate('company.state'),
            'country_id' => $this->translate('company.country'),
            'postal_code' => $this->translate('company.postal_code'),
            'company_size_id' => $this->translate('company.company_size'),
            'founded_year' => $this->translate('company.founded_year'),
            'industry' => $this->translate('company.industry'),
            'social_media' => $this->translate('company.social_media'),
            'social_media.linkedin' => $this->translate('company.linkedin'),
            'social_media.twitter' => $this->translate('company.twitter'),
            'social_media.facebook' => $this->translate('company.facebook'),
            'social_media.instagram' => $this->translate('company.instagram'),
        ];
    }
}
