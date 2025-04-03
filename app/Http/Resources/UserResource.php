<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the user.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The first name of the user.
             *
             * @var string $first_name
             * @example "John"
             */
            'first_name' => $this->first_name,

            /**
             * The last name of the user.
             *
             * @var string $last_name
             * @example "Doe"
             */
            'last_name' => $this->last_name,

            /**
             * The full name of the user (first name + last name).
             *
             * @var string $full_name
             * @example "John Doe"
             */
            'full_name' => $this->full_name,

            /**
             * The phone number of the user.
             *
             * @var string|null $phone
             * @example "+1234567890"
             */
            'phone' => $this->phone,

            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => $this->email,

            /**
             * The role of the user.
             *
             * @var string $role
             * @example "candidate"
             */
            'role' => $this->role,

            /**
             * The human-readable label for the user's role.
             *
             * @var string|null $role_label
             * @example "Job Seeker"
             */
            'role_label' => $this->role ? $this->role->label() : null,

            /**
             * The profile picture file path of the user.
             *
             * @var string|null $profile_picture
             * @example "profile-pictures/profile_1234567890.jpg"
             */
            'profile_picture' => $this->profile_picture,

            /**
             * The full URL to the user's profile picture.
             *
             * @var string|null $profile_picture_url
             * @example "https://example.com/storage/profile-pictures/profile_1234567890.jpg"
             */
            'profile_picture_url' => $this->profile_picture_url,

            /**
             * Whether the user account is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The timestamp when the user's email was verified.
             *
             * @var string|null $email_verified_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'email_verified_at' => $this->email_verified_at,

            /**
             * The timestamp when the user was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the user was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,

            /**
             * The timestamp when the user was deleted (soft delete).
             *
             * @var string|null $deleted_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'deleted_at' => $this->deleted_at,

            /**
             * The candidate profile associated with the user.
             *
             * @var array|null $candidate_profile
             */
            'candidate_profile' => $this->when($this->candidateProfile, new CandidateProfileResource($this->candidateProfile)),

            /**
             * The companies associated with the user.
             *
             * @var array|null $companies
             */
            'companies' => $this->when($this->companies, CompanyResource::collection($this->companies)),

            /**
             * The roles assigned to the user via Spatie Permissions.
             *
             * @var array $roles
             */
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->pluck('name');
            }),

            /**
             * The permissions assigned to the user via Spatie Permissions.
             *
             * @var array $permissions
             */
            'permissions' => $this->whenLoaded('permissions', function() {
                return $this->getAllPermissions()->pluck('name');
            }),
        ];
    }
}

