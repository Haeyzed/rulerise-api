<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Services\Storage\StorageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\AccountDeactivatedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * @var StorageService
     */
    protected StorageService $storageService;

    /**
     * @var CandidateService
     */
    protected CandidateService $candidateService;

    /**
     * @var CompanyService
     */
    protected CompanyService $companyService;

    /**
     * AuthService constructor.
     *
     * @param StorageService $storageService
     * @param CandidateService|null $candidateService
     * @param CompanyService|null $companyService
     */
    public function __construct(
        StorageService $storageService,
        CandidateService $candidateService = null,
        CompanyService $companyService = null
    ) {
        $this->storageService = $storageService;
        $this->candidateService = $candidateService ?? app(CandidateService::class);
        $this->companyService = $companyService ?? app(CompanyService::class);
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            // Create candidate profile if role is candidate
            if ($user->role->value === UserRoleEnum::CANDIDATE->value) {
                $candidateData = [
                    'title' => $data['title'] ?? null,
                    'is_public' => $data['is_public'] ?? false,
                    'is_available' => $data['is_available'] ?? true,
                    'bio' => $data['bio'] ?? null,
                    'headline' => $data['headline'] ?? null,
                    'education_level_id' => $data['education_level_id'] ?? null,
                ];

                $user->candidateProfile()->create($candidateData);
            }

            // Create company if role is employer
            if ($user->role->value === UserRoleEnum::EMPLOYER->value) {
                $companyData = [
                    'name' => $data['company_name'],
                    'slug' => Str::slug($data['company_name']) . '-' . Str::lower(Str::random(6)),
                    'industry' => $data['company_industry'] ?? null,
                    'company_size_id' => $data['company_size_id'] ?? null,
                    'is_verified' => false,
                    'email' => $data['company_email'] ?? null,
                    'phone' => $data['company_phone'] ?? null,
                    'founded_year' => $data['company_founded_year'] ?? null,
                    'website' => $data['company_website'] ?? null,
                    'description' => $data['company_description'] ?? null,
                    'address' => $data['company_address'] ?? null,
                    'city_id' => $data['company_city_id'] ?? null,
                    'state_id' => $data['company_state_id'] ?? null,
                    'country_id' => $data['company_country_id'] ?? null,
                    'postal_code' => $data['company_postal_code'] ?? null,
                ];

                // Handle logo upload if provided
                if (isset($data['company_logo']) && $data['company_logo'] instanceof UploadedFile) {
                    $companyData['logo'] = $this->uploadImage(
                        $data['company_logo'],
                        config('filestorage.paths.company_logos')
                    );
                }

                $user->companies()->create($companyData);
            }

            // Assign default role
            $user->assignRole($user->role);

            // Send verification email
            $user->notify(new VerifyEmailNotification);

            // Fire registered event
            event(new Registered($user));

            return $user;
        });
    }

    /**
     * Register a new candidate.
     *
     * @param array $data
     * @return array
     */
    public function registerCandidate(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Ensure role is set to candidate
            $data['role'] = UserRoleEnum::CANDIDATE->value;

            // Create the user
            $user = $this->register($data);

            // Process skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    $this->candidateService->addOrUpdateSkill($user->candidateProfile, $skill);
                }
            }

            // Load relationships
            $user->load('candidateProfile', 'candidateProfile.skills', 'candidateProfile.skills.skill', 'roles', 'permissions');

            // Login the user
            $loginResult = $this->login([
                'email' => $data['email'],
                'password' => $data['password']
            ]);

            return [
                'user' => $user,
                'token' => $loginResult['token'],
                'token_type' => $loginResult['token_type'],
                'expires_in' => $loginResult['expires_in'],
            ];
        });
    }

    /**
     * Register a new employer.
     *
     * @param array $data
     * @return array
     */
    public function registerEmployer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Ensure role is set to employer
            $data['role'] = UserRoleEnum::EMPLOYER->value;

            // Create the user
            $user = $this->register($data);

            // Get the company that was created during registration
            $company = $user->companies()->first();

            // Load relationships
            $user->load('companies', 'roles', 'permissions');

            // Login the user
            $loginResult = $this->login([
                'email' => $data['email'],
                'password' => $data['password']
            ]);

            return [
                'user' => $user,
                'company' => $company,
                'token' => $loginResult['token'],
                'token_type' => $loginResult['token_type'],
                'expires_in' => $loginResult['expires_in'],
            ];
        });
    }

    /**
     * Login a user.
     *
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return null;
            }

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return null;
            }

            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ];
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Logout a user.
     *
     * @return bool
     */
    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }

    /**
     * Refresh a token.
     *
     * @return array|null
     */
    public function refreshToken(): ?array
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ];
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Send password reset link.
     *
     * @param string $email
     * @return string
     */
    public function sendPasswordResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email], function ($user, $token) {
            $user->notify(new ResetPasswordNotification($token));
        });

        return $status;
    }

    /**
     * Reset password.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }

    /**
     * Change password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    /**
     * Verify email.
     *
     * @param string $id
     * @param string $hash
     * @return bool
     */
    public function verifyEmail(string $id, string $hash): bool
    {
        $user = User::query()->find($id);

        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        $user->markEmailAsVerified();

        return true;
    }

    /**
     * Resend verification email.
     *
     * @param User $user
     * @return bool
     */
    public function resendVerificationEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->notify(new VerifyEmailNotification);

        return true;
    }

    /**
     * Get user profile.
     *
     * @param User $user
     * @return array
     */
    public function getProfile(User $user): array
    {
        $profile = [
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->roles->pluck('name'),
        ];

        if ($user->role->value === UserRoleEnum::CANDIDATE->value) {
            $profile['candidate_profile'] = $user->candidateProfile()->with([
                'educationLevel',
                'skills.skill',
                'educations',
                'experiences',
                'projects',
                'certifications',
                'languages.language',
                'resumes',
            ])->first();
        } elseif ($user->role->value === UserRoleEnum::EMPLOYER->value) {
            $profile['companies'] = $user->companies()->with([
                'companySize',
                'country',
                'state',
                'city',
            ])->get();
        }

        return $profile;
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Update user data
            $user->update([
                'first_name' => $data['first_name'] ?? $user->first_name,
                'last_name' => $data['last_name'] ?? $user->last_name,
                'phone' => $data['phone'] ?? $user->phone,
            ]);

            // Handle profile picture upload
            if (isset($data['profile_picture']) && $data['profile_picture'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old profile picture if exists
                if ($user->profile_picture) {
                    $this->storageService->delete($user->profile_picture);
                }

                $data['profile_picture'] = $this->uploadImage(
                    $data['profile_picture'],
                    config('filestorage.paths.profile_pictures')
                );

                $user->update(['profile_picture' => $data['profile_picture']]);
            }

            return $user;
        });
    }

    /**
     * Check if email exists.
     *
     * @param string $email
     * @param int|null $excludeUserId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        return User::where('email', $email)
            ->when($excludeUserId, function ($query) use ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            })
            ->exists();
    }

    /**
     * Deactivate account.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function deactivateAccount(User $user, string $password): bool
    {
        // Verify password
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        // Deactivate account
        $user->update([
            'is_active' => false,
            'deactivated_at' => now(),
        ]);

        // Notify user
        $user->notify(new AccountDeactivatedNotification);

        return true;
    }

    /**
     * Reactivate account.
     *
     * @param User $user
     * @return bool
     */
    public function reactivateAccount(User $user): bool
    {
        return $user->update([
            'is_active' => true,
            'deactivated_at' => null,
        ]);
    }

    /**
     * Delete account.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function deleteAccount(User $user, string $password): bool
    {
        // Verify password
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        return DB::transaction(function () use ($user) {
            // Delete user
            return $user->delete();
        });
    }

    /**
     * Generate two-factor authentication secret.
     *
     * @param User $user
     * @return array
     */
    public function generateTwoFactorSecret(User $user): array
    {
        $google2fa = app('pragmarx.google2fa');

        // Generate new secret
        $secret = $google2fa->generateSecretKey();

        // Store secret
        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($this->generateRecoveryCodes()),
        ]);

        // Generate QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'recovery_codes' => json_decode($user->two_factor_recovery_codes),
        ];
    }

    /**
     * Enable two-factor authentication.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function enableTwoFactor(User $user, string $code): bool
    {
        $google2fa = app('pragmarx.google2fa');

        // Verify code
        $valid = $google2fa->verifyKey($user->two_factor_secret, $code);

        if ($valid) {
            $user->update([
                'two_factor_enabled' => true,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Disable two-factor authentication.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function disableTwoFactor(User $user, string $password): bool
    {
        // Verify password
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return true;
    }

    /**
     * Verify two-factor authentication code.
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyTwoFactorCode(User $user, string $code): bool
    {
        $google2fa = app('pragmarx.google2fa');

        // Check if code is a recovery code
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);

        if (in_array($code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$code]);

            $user->update([
                'two_factor_recovery_codes' => json_encode($recoveryCodes),
            ]);

            return true;
        }

        // Verify code
        return $google2fa->verifyKey($user->two_factor_secret, $code);
    }

    /**
     * Generate recovery codes.
     *
     * @param int $count
     * @return array
     */
    private function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::random(10);
        }

        return $codes;
    }

    /**
     * Upload an image to storage.
     *
     * @param UploadedFile $image The image file to upload.
     * @param string $path The storage path.
     * @param array $options Additional options for the upload.
     * @return string The path to the uploaded image.
     */
    private function uploadImage(UploadedFile $image, string $path, array $options = []): string
    {
        return $this->storageService->upload($image, $path, $options);
    }
}

