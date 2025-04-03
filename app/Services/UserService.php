<?php

namespace App\Services;

use App\Enums\UserRoleEnum;
use App\Models\Job;
use App\Models\User;
use App\Models\CandidateProfile;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserService
{
    /**
     * List users based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['role']), function ($query) use ($filters) {
                $query->where('role', $filters['role']);
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['email_verified']), function ($query) use ($filters) {
                if ($filters['email_verified']) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(isset($filters['created_from']), function ($query) use ($filters) {
                $query->where('created_at', '>=', $filters['created_from']);
            })
            ->when(isset($filters['created_to']), function ($query) use ($filters) {
                $query->where('created_at', '<=', $filters['created_to']);
            })
            ->when(isset($filters['country']), function ($query) use ($filters) {
                $query->whereHas('candidateProfile', function ($q) use ($filters) {
                    $q->where('country', $filters['country']);
                });
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest();
            })
            ->when(isset($filters['trashed']) && $filters['trashed'], function ($query) {
                $query->onlyTrashed();
            })
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Handle profile picture upload
            if (isset($data['profile_picture']) && $data['profile_picture'] instanceof UploadedFile) {
                $data['profile_picture'] = $this->uploadProfilePicture($data['profile_picture']);
            }

            // Create user
            $user = User::query()->create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? UserRoleEnum::CANDIDATE->value,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'profile_picture' => $data['profile_picture'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'email_verified_at' => $data['email_verified'] ?? false ? now() : null,
            ]);

            // Create candidate profile if role is candidate
            if (($data['role'] ?? null) === UserRoleEnum::CANDIDATE->value) {
                $user->candidateProfile()->create([
                    'title' => $data['title'] ?? null,
                    'bio' => $data['bio'] ?? null,
                ]);
            }

            // Create company if role is employer
            if (($data['role'] ?? null) === UserRoleEnum::EMPLOYER->value && isset($data['company_name'])) {
                $user->companies()->create([
                    'name' => $data['company_name'],
                    'slug' => Str::slug($data['company_name']) . '-' . Str::lower(Str::random(6)),
                ]);
            }

            return $user;
        });
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Handle profile picture upload
            if (isset($data['profile_picture']) && $data['profile_picture'] instanceof UploadedFile) {
                // Delete old profile picture if exists
                if ($user->profile_picture) {
                    Storage::delete('public/' . $user->profile_picture);
                }

                $data['profile_picture'] = $this->uploadProfilePicture($data['profile_picture']);
            }

            // Update password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Update user
            $user->update($data);

            return $user;
        });
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Force delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Delete profile picture if exists
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }

            // Delete related records
            if ($user->candidateProfile) {
                // Delete candidate resumes
                foreach ($user->candidateProfile->resumes as $resume) {
                    Storage::delete('public/' . $resume->file_path);
                }
            }

            return $user->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param User $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->restore();
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function changePassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Change user status.
     *
     * @param User $user
     * @param bool $isActive
     * @return bool
     */
    public function changeStatus(User $user, bool $isActive): bool
    {
        return $user->update([
            'is_active' => $isActive,
        ]);
    }

    /**
     * Verify user email.
     *
     * @param User $user
     * @return bool
     */
    public function verifyEmail(User $user): bool
    {
        return $user->update([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Change user role.
     *
     * @param User $user
     * @param string $role
     * @return User
     */
    public function changeRole(User $user, string $role): User
    {
        return DB::transaction(function () use ($user, $role) {
            $user->update(['role' => $role]);

            // Create candidate profile if changing to candidate and doesn't exist
            if ($role === UserRoleEnum::CANDIDATE->value && !$user->candidateProfile) {
                $user->candidateProfile()->create();
            }

            return $user;
        });
    }

    /**
     * Get user statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => User::query()->count(),
            'active' => User::query()->where('is_active', true)->count(),
            'inactive' => User::query()->where('is_active', false)->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
            'unverified' => User::query()->whereNull('email_verified_at')->count(),
            'candidates' => User::query()->where('role', UserRoleEnum::CANDIDATE->value)->count(),
            'employers' => User::query()->where('role', UserRoleEnum::EMPLOYER->value)->count(),
            'admins' => User::query()->where('role', UserRoleEnum::ADMIN->value)->count(),
            'new_today' => User::query()->whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => User::query()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => User::query()->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * Get user activity.
     *
     * @param User $user
     * @return array
     */
    public function getUserActivity(User $user): array
    {
        $activity = [
            'last_login' => null, // Assuming you track this in a separate table
            'job_applications' => [],
            'job_bookmarks' => [],
            'company_reviews' => [],
        ];

        if ($user->role === UserRoleEnum::CANDIDATE->value && $user->candidateProfile) {
            $activity['job_applications'] = $user->candidateProfile->applications()
                ->with('job:id,title,company_id', 'job.company:id,name')
                ->latest()
                ->take(10)
                ->get();

            $activity['job_bookmarks'] = $user->jobBookmarks()
                ->with('job:id,title,company_id', 'job.company:id,name')
                ->latest()
                ->take(10)
                ->get();
        }

        $activity['company_reviews'] = $user->companyReviews()
            ->with('company:id,name')
            ->latest()
            ->take(10)
            ->get();

        return $activity;
    }

    /**
     * Upload profile picture.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function uploadProfilePicture(UploadedFile $file): string
    {
        $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('profile-pictures', $filename, 'public');
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
        return User::query()->where('email', $email)
            ->when($excludeUserId, function ($query) use ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            })
            ->exists();
    }

    /**
     * Get user dashboard data.
     *
     * @param User $user
     * @return array
     */
    public function getDashboardData(User $user): array
    {
        $data = [
            'user' => $user,
        ];

        if ($user->role === UserRoleEnum::CANDIDATE->value && $user->candidateProfile) {
            $data['profile_completion'] = $this->calculateProfileCompletion($user->candidateProfile);
            $data['recent_applications'] = $user->candidateProfile->applications()
                ->with('job:id,title,company_id', 'job.company:id,name')
                ->latest()
                ->take(5)
                ->get();
            $data['recommended_jobs'] = $this->getRecommendedJobs($user->candidateProfile);
            $data['profile_views'] = $user->candidateProfile->views_count;
        } elseif ($user->role === UserRoleEnum::EMPLOYER->value) {
            $companies = $user->companies;
            $data['companies'] = $companies;

            if ($companies->isNotEmpty()) {
                $data['active_jobs'] = $companies->flatMap(function ($company) {
                    return $company->jobs()->active()->get();
                })->count();

                $data['total_applications'] = $companies->flatMap(function ($company) {
                    return $company->jobs->flatMap(function ($job) {
                        return $job->applications;
                    });
                })->count();

                $data['recent_applications'] = $companies->flatMap(function ($company) {
                    return $company->jobs->flatMap(function ($job) {
                        return $job->applications()->with('candidate.user', 'job')->latest()->take(5)->get();
                    });
                })->sortByDesc('created_at')->take(5)->values();
            }
        }

        return $data;
    }

    /**
     * Calculate profile completion percentage.
     *
     * @param CandidateProfile $profile
     * @return int
     */
    private function calculateProfileCompletion(CandidateProfile $profile): int
    {
        $fields = [
            'title', 'bio', 'date_of_birth', 'gender', 'experience_years',
            'current_salary', 'expected_salary', 'education_level_id',
            'address', 'city', 'state', 'country', 'postal_code',
        ];

        $socialFields = [
            'facebook_url', 'twitter_url', 'linkedin_url', 'github_url', 'portfolio_url',
        ];

        $filledFields = 0;
        $totalFields = count($fields) + 5; // 5 for related entities

        // Check basic fields
        foreach ($fields as $field) {
            if (!empty($profile->$field)) {
                $filledFields++;
            }
        }

        // Check social fields (max 3 points)
        $filledSocialFields = 0;
        foreach ($socialFields as $field) {
            if (!empty($profile->$field)) {
                $filledSocialFields++;
            }
        }
        $filledFields += min(3, $filledSocialFields);

        // Check related entities
        if ($profile->skills()->count() > 0) $filledFields++;
        if ($profile->educations()->count() > 0) $filledFields++;
        if ($profile->experiences()->count() > 0) $filledFields++;
        if ($profile->projects()->count() > 0) $filledFields++;
        if ($profile->resumes()->count() > 0) $filledFields++;

        return (int) round(($filledFields / $totalFields) * 100);
    }

    /**
     * Get recommended jobs for a candidate.
     *
     * @param CandidateProfile $profile
     * @param int $limit
     * @return Collection
     */
    private function getRecommendedJobs(CandidateProfile $profile, int $limit = 5): Collection
    {
        // Get candidate skills
        $skillIds = $profile->skills()->pluck('skill_id')->toArray();

        // Get candidate's education level
        $educationLevelId = $profile->education_level_id;

        // Get jobs matching skills and education level
        return Job::query()
            ->active()
            ->where(function ($query) use ($profile) {
                $query->where('country', $profile->country)
                    ->orWhere('is_remote', true);
            })
            ->when($skillIds, function ($query) use ($skillIds) {
                $query->whereHas('skills', function ($q) use ($skillIds) {
                    $q->whereIn('skill_id', $skillIds);
                });
            })
            ->when($educationLevelId, function ($query) use ($educationLevelId) {
                $query->where(function ($q) use ($educationLevelId) {
                    $q->where('education_level_id', $educationLevelId)
                      ->orWhereNull('education_level_id');
                });
            })
            ->with(['company:id,name,logo', 'jobType:id,name', 'skills:id,name'])
            ->latest()
            ->take($limit)
            ->get();
    }
}

