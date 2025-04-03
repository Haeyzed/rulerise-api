<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\CandidateResume;
use App\Models\CandidateSkill;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateProject;
use App\Models\CandidateCertification;
use App\Models\CandidateLanguage;
use App\Models\Job;
use App\Models\Skill;
use App\Enums\ProficiencyLevelEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    /**
     * List candidates based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return CandidateProfile::query()
            ->with(['user', 'educationLevel'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            })
            ->when(isset($filters['education_level_id']), function ($query) use ($filters) {
                $query->where('education_level_id', $filters['education_level_id']);
            })
            ->when(isset($filters['experience_years_min']), function ($query) use ($filters) {
                $query->where('experience_years', '>=', $filters['experience_years_min']);
            })
            ->when(isset($filters['experience_years_max']), function ($query) use ($filters) {
                $query->where('experience_years', '<=', $filters['experience_years_max']);
            })
            ->when(isset($filters['expected_salary_min']), function ($query) use ($filters) {
                $query->where('expected_salary', '>=', $filters['expected_salary_min']);
            })
            ->when(isset($filters['expected_salary_max']), function ($query) use ($filters) {
                $query->where('expected_salary', '<=', $filters['expected_salary_max']);
            })
            ->when(isset($filters['location']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('city', 'like', "%{$filters['location']}%")
                        ->orWhere('state', 'like', "%{$filters['location']}%")
                        ->orWhere('country', 'like', "%{$filters['location']}%");
                });
            })
            ->when(isset($filters['country']), function ($query) use ($filters) {
                $query->where('country', $filters['country']);
            })
            ->when(isset($filters['is_remote_preferred']), function ($query) use ($filters) {
                $query->where('is_remote_preferred', $filters['is_remote_preferred']);
            })
            ->when(isset($filters['is_public']), function ($query) use ($filters) {
                $query->where('is_public', $filters['is_public']);
            })
            ->when(isset($filters['is_available']), function ($query) use ($filters) {
                $query->where('is_available', $filters['is_available']);
            })
            ->when(isset($filters['is_featured']), function ($query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            ->when(isset($filters['skill_ids']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($q) use ($filters) {
                    $q->whereIn('skill_id', $filters['skill_ids']);
                });
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Create a new candidate profile.
     *
     * @param array $data
     * @return CandidateProfile
     */
    public function create(array $data): CandidateProfile
    {
        return DB::transaction(function () use ($data) {
            // Create candidate profile
            $candidateProfile = CandidateProfile::query()->create($data);

            // Add skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    $this->addOrUpdateSkill($candidateProfile, $skill);
                }
            }

            // Add educations if provided
            if (isset($data['educations']) && is_array($data['educations'])) {
                foreach ($data['educations'] as $education) {
                    $candidateProfile->educations()->create($education);
                }
            }

            // Add experiences if provided
            if (isset($data['experiences']) && is_array($data['experiences'])) {
                foreach ($data['experiences'] as $experience) {
                    $candidateProfile->experiences()->create($experience);
                }
            }

            // Add projects if provided
            if (isset($data['projects']) && is_array($data['projects'])) {
                foreach ($data['projects'] as $project) {
                    $candidateProfile->projects()->create($project);
                }
            }

            // Add certifications if provided
            if (isset($data['certifications']) && is_array($data['certifications'])) {
                foreach ($data['certifications'] as $certification) {
                    $candidateProfile->certifications()->create($certification);
                }
            }

            // Add languages if provided
            if (isset($data['languages']) && is_array($data['languages'])) {
                foreach ($data['languages'] as $language) {
                    $candidateProfile->languages()->create($language);
                }
            }

            return $candidateProfile;
        });
    }

    /**
     * Update an existing candidate profile.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @return CandidateProfile
     */
    public function update(CandidateProfile $candidateProfile, array $data): CandidateProfile
    {
        return DB::transaction(function () use ($candidateProfile, $data) {
            // Update candidate profile
            $candidateProfile->update($data);

            return $candidateProfile;
        });
    }

    /**
     * Add or update a skill for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @return CandidateSkill
     */
    public function addOrUpdateSkill(CandidateProfile $candidateProfile, array $data): CandidateSkill
    {
        try {
            // Check if skill_id exists in the data
            $skillId = $data['id'] ?? $data['skill_id'] ?? null;

            if (!$skillId) {
                throw new \InvalidArgumentException("Skill ID is required");
            }

            // Find existing skill or create new one
            $candidateSkill = $candidateProfile->skills()
                ->where('skill_id', $skillId)
                ->first();

            if (!$candidateSkill) {
                $candidateSkill = new CandidateSkill([
                    'candidate_id' => $candidateProfile->id,
                    'skill_id' => $skillId,
                ]);
            }

            // Update level if provided
            if (isset($data['level'])) {
                $candidateSkill->level = $data['level'];
            }

            // Update proficiency_level if provided
            if (isset($data['proficiency_level'])) {
                $candidateSkill->proficiency_level = $data['proficiency_level'];
            }

            $candidateSkill->save();
            return $candidateSkill;
        } catch (\Exception $e) {
            Log::error('Error adding/updating candidate skill: ' . $e->getMessage(), [
                'candidate_id' => $candidateProfile->id,
                'skill_data' => $data,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Remove a skill from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $skillId
     * @return bool
     */
    public function removeSkill(CandidateProfile $candidateProfile, int $skillId): bool
    {
        return $candidateProfile->skills()
            ->where('skill_id', $skillId)
            ->delete();
    }

    /**
     * Update multiple skills for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $skills
     * @return array
     */
    public function updateSkills(CandidateProfile $candidateProfile, array $skills): array
    {
        return DB::transaction(function () use ($candidateProfile, $skills) {
            $updatedSkills = [];
            foreach ($skills as $skillData) {
                $updatedSkills[] = $this->addOrUpdateSkill($candidateProfile, $skillData);
            }

            return $updatedSkills;
        });
    }

    /**
     * Add or update an education for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @param int|null $educationId
     * @return CandidateEducation
     */
    public function addOrUpdateEducation(CandidateProfile $candidateProfile, array $data, ?int $educationId = null): CandidateEducation
    {
        if ($educationId) {
            $education = $candidateProfile->educations()->findOrFail($educationId);
            $education->update($data);
        } else {
            $education = $candidateProfile->educations()->create($data);
        }

        return $education;
    }

    /**
     * Remove an education from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $educationId
     * @return bool
     */
    public function removeEducation(CandidateProfile $candidateProfile, int $educationId): bool
    {
        $education = $candidateProfile->educations()->findOrFail($educationId);
        return $education->delete();
    }

    /**
     * Add or update an experience for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @param int|null $experienceId
     * @return CandidateExperience
     */
    public function addOrUpdateExperience(CandidateProfile $candidateProfile, array $data, ?int $experienceId = null): CandidateExperience
    {
        if ($experienceId) {
            $experience = $candidateProfile->experiences()->findOrFail($experienceId);
            $experience->update($data);
        } else {
            $experience = $candidateProfile->experiences()->create($data);
        }

        return $experience;
    }

    /**
     * Remove an experience from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $experienceId
     * @return bool
     */
    public function removeExperience(CandidateProfile $candidateProfile, int $experienceId): bool
    {
        $experience = $candidateProfile->experiences()->findOrFail($experienceId);
        return $experience->delete();
    }

    /**
     * Add or update a project for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @param int|null $projectId
     * @return CandidateProject
     */
    public function addOrUpdateProject(CandidateProfile $candidateProfile, array $data, ?int $projectId = null): CandidateProject
    {
        if ($projectId) {
            $project = $candidateProfile->projects()->findOrFail($projectId);
            $project->update($data);
        } else {
            $project = $candidateProfile->projects()->create($data);
        }

        return $project;
    }

    /**
     * Remove a project from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $projectId
     * @return bool
     */
    public function removeProject(CandidateProfile $candidateProfile, int $projectId): bool
    {
        $project = $candidateProfile->projects()->findOrFail($projectId);
        return $project->delete();
    }

    /**
     * Add or update a certification for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @param int|null $certificationId
     * @return CandidateCertification
     */
    public function addOrUpdateCertification(CandidateProfile $candidateProfile, array $data, ?int $certificationId = null): CandidateCertification
    {
        if ($certificationId) {
            $certification = $candidateProfile->certifications()->findOrFail($certificationId);
            $certification->update($data);
        } else {
            $certification = $candidateProfile->certifications()->create($data);
        }

        return $certification;
    }

    /**
     * Remove a certification from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $certificationId
     * @return bool
     */
    public function removeCertification(CandidateProfile $candidateProfile, int $certificationId): bool
    {
        $certification = $candidateProfile->certifications()->findOrFail($certificationId);
        return $certification->delete();
    }

    /**
     * Add or update a language for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param array $data
     * @param int|null $languageId
     * @return CandidateLanguage
     */
    public function addOrUpdateLanguage(CandidateProfile $candidateProfile, array $data, ?int $languageId = null): CandidateLanguage
    {
        if ($languageId) {
            $language = $candidateProfile->languages()->findOrFail($languageId);
            $language->update($data);
        } else {
            $language = $candidateProfile->languages()->create($data);
        }

        return $language;
    }

    /**
     * Remove a language from a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $languageId
     * @return bool
     */
    public function removeLanguage(CandidateProfile $candidateProfile, int $languageId): bool
    {
        $language = $candidateProfile->languages()->findOrFail($languageId);
        return $language->delete();
    }

    /**
     * Upload a resume for a candidate.
     *
     * @param CandidateProfile $candidateProfile
     * @param UploadedFile $file
     * @param array $data
     * @return CandidateResume
     */
    public function uploadResume(CandidateProfile $candidateProfile, UploadedFile $file, array $data): CandidateResume
    {
        return DB::transaction(function () use ($candidateProfile, $file, $data) {
            // Upload file
            $filename = 'resume_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('resumes', $filename, 'public');

            // If this is the first resume or is_primary is true, set all other resumes to not primary
            if ($data['is_primary'] ?? false) {
                $candidateProfile->resumes()->update(['is_primary' => false]);
            }

            // Create resume record
            return $candidateProfile->resumes()->create([
                'title' => $data['title'],
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'is_primary' => $data['is_primary'] ?? false,
            ]);
        });
    }

    /**
     * Delete a resume.
     *
     * @param CandidateResume $resume
     * @return bool
     */
    public function deleteResume(CandidateResume $resume): bool
    {
        return DB::transaction(function () use ($resume) {
            // Delete file
            Storage::delete('public/' . $resume->file_path);

            // Delete record
            return $resume->delete();
        });
    }

    /**
     * Set a resume as primary.
     *
     * @param CandidateResume $resume
     * @return bool
     */
    public function setResumePrimary(CandidateResume $resume): bool
    {
        return DB::transaction(function () use ($resume) {
            // Set all resumes to not primary
            $resume->candidate->resumes()->update(['is_primary' => false]);

            // Set this resume as primary
            return $resume->update(['is_primary' => true]);
        });
    }

    /**
     * Toggle candidate featured status.
     *
     * @param CandidateProfile $candidateProfile
     * @return bool
     */
    public function toggleFeatured(CandidateProfile $candidateProfile): bool
    {
        return $candidateProfile->update([
            'is_featured' => !$candidateProfile->is_featured,
        ]);
    }

    /**
     * Toggle candidate availability status.
     *
     * @param CandidateProfile $candidateProfile
     * @return bool
     */
    public function toggleAvailability(CandidateProfile $candidateProfile): bool
    {
        return $candidateProfile->update([
            'is_available' => !$candidateProfile->is_available,
        ]);
    }

    /**
     * Toggle candidate public status.
     *
     * @param CandidateProfile $candidateProfile
     * @return bool
     */
    public function togglePublic(CandidateProfile $candidateProfile): bool
    {
        return $candidateProfile->update([
            'is_public' => !$candidateProfile->is_public,
        ]);
    }

    /**
     * Calculate profile completion percentage.
     *
     * @param CandidateProfile $candidateProfile
     * @return int
     */
    public function calculateProfileCompletion(CandidateProfile $candidateProfile): int
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
            if (!empty($candidateProfile->$field)) {
                $filledFields++;
            }
        }

        // Check social fields (max 3 points)
        $filledSocialFields = 0;
        foreach ($socialFields as $field) {
            if (!empty($candidateProfile->$field)) {
                $filledSocialFields++;
            }
        }
        $filledFields += min(3, $filledSocialFields);

        // Check related entities
        if ($candidateProfile->skills()->count() > 0) $filledFields++;
        if ($candidateProfile->educations()->count() > 0) $filledFields++;
        if ($candidateProfile->experiences()->count() > 0) $filledFields++;
        if ($candidateProfile->projects()->count() > 0) $filledFields++;
        if ($candidateProfile->resumes()->count() > 0) $filledFields++;

        return (int) round(($filledFields / $totalFields) * 100);
    }

    /**
     * Get candidate statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => CandidateProfile::query()->count(),
            'public' => CandidateProfile::query()->where('is_public', true)->count(),
            'available' => CandidateProfile::query()->where('is_available', true)->count(),
            'featured' => CandidateProfile::query()->where('is_featured', true)->count(),
            'remote_preferred' => CandidateProfile::query()->where('is_remote_preferred', true)->count(),
            'by_experience' => [
                'entry_level' => CandidateProfile::query()->where('experience_years', '<', 2)->count(),
                'mid_level' => CandidateProfile::query()->whereBetween('experience_years', [2, 5])->count(),
                'senior' => CandidateProfile::query()->where('experience_years', '>', 5)->count(),
            ],
            'by_education' => DB::table('candidate_profiles')
                ->join('education_levels', 'candidate_profiles.education_level_id', '=', 'education_levels.id')
                ->select('education_levels.name', DB::raw('count(*) as total'))
                ->groupBy('education_levels.name')
                ->get(),
            'top_skills' => DB::table('candidate_skills')
                ->join('skills', 'candidate_skills.skill_id', '=', 'skills.id')
                ->select('skills.name', DB::raw('count(*) as total'))
                ->groupBy('skills.name')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'new_today' => CandidateProfile::query()->whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => CandidateProfile::query()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => CandidateProfile::query()->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * Get recommended candidates for a job.
     *
     * @param int $jobId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendedCandidatesForJob(int $jobId, int $limit = 10): Collection
    {
        $job = Job::with('skills')->findOrFail($jobId);

        // Get job skills
        $jobSkillIds = $job->skills->pluck('id')->toArray();

        // Find candidates matching skills, education level, and location
        return CandidateProfile::query()
            ->with(['user:id,first_name,last_name,email,profile_picture', 'skills.skill', 'educationLevel'])
            ->where('is_public', true)
            ->where('is_available', true)
            ->where(function ($query) use ($job) {
                $query->where('country', $job->country)
                    ->orWhere('is_remote_preferred', true);
            })
            ->when($job->education_level_id, function ($query) use ($job) {
                $query->where(function ($q) use ($job) {
                    $q->where('education_level_id', '>=', $job->education_level_id)
                        ->orWhereNull('education_level_id');
                });
            })
            ->when($jobSkillIds, function ($query) use ($jobSkillIds) {
                $query->whereHas('skills', function ($q) use ($jobSkillIds) {
                    $q->whereIn('skill_id', $jobSkillIds);
                });
            })
            ->withCount(['skills as matching_skills_count' => function ($query) use ($jobSkillIds) {
                $query->whereIn('skill_id', $jobSkillIds);
            }])
            ->orderBy('matching_skills_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get candidate job application history.
     *
     * @param CandidateProfile $candidateProfile
     * @return Collection
     */
    public function getJobApplicationHistory(CandidateProfile $candidateProfile): Collection
    {
        return $candidateProfile->applications()
            ->with(['job:id,title,company_id,location,created_at', 'job.company:id,name'])
            ->latest()
            ->get();
    }

    /**
     * Get candidate job application statistics.
     *
     * @param CandidateProfile $candidateProfile
     * @return array
     */
    public function getJobApplicationStats(CandidateProfile $candidateProfile): array
    {
        $applications = $candidateProfile->applications;

        return [
            'total' => $applications->count(),
            'by_status' => [
                'pending' => $applications->where('status', 'pending')->count(),
                'reviewed' => $applications->where('status', 'reviewed')->count(),
                'shortlisted' => $applications->where('status', 'shortlisted')->count(),
                'rejected' => $applications->where('status', 'rejected')->count(),
                'interview' => $applications->where('status', 'interview')->count(),
                'offered' => $applications->where('status', 'offered')->count(),
                'hired' => $applications->where('status', 'hired')->count(),
                'withdrawn' => $applications->where('status', 'withdrawn')->count(),
            ],
            'success_rate' => $applications->count() > 0
                ? round(($applications->whereIn('status', ['hired', 'offered'])->count() / $applications->count()) * 100, 2)
                : 0,
            'interview_rate' => $applications->count() > 0
                ? round(($applications->where('status', 'interview')->count() / $applications->count()) * 100, 2)
                : 0,
            'rejection_rate' => $applications->count() > 0
                ? round(($applications->where('status', 'rejected')->count() / $applications->count()) * 100, 2)
                : 0,
            'by_job_type' => $applications->groupBy('job.job_type_id')
                ->map(function ($items, $key) {
                    return [
                        'job_type_id' => $key,
                        'job_type_name' => $items->first()->job->jobType->name ?? 'Unknown',
                        'count' => $items->count(),
                    ];
                })->values(),
            'by_company' => $applications->groupBy('job.company_id')
                ->map(function ($items, $key) {
                    return [
                        'company_id' => $key,
                        'company_name' => $items->first()->job->company->name ?? 'Unknown',
                        'count' => $items->count(),
                    ];
                })->values(),
            'recent_activity' => $applications->sortByDesc('created_at')->take(5),
        ];
    }

    /**
     * Get skill suggestions for a candidate based on their profile.
     *
     * @param CandidateProfile $candidateProfile
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getSkillSuggestions(CandidateProfile $candidateProfile, int $limit = 10): \Illuminate\Support\Collection
    {
        // Get candidate's current skills
        $currentSkillIds = $candidateProfile->skills()->pluck('skill_id')->toArray();

        // Get candidate's experience and education info
        $jobTitles = $candidateProfile->experiences()->pluck('job_title')->toArray();
        $educationFields = $candidateProfile->educations()->pluck('field_of_study')->toArray();

        // Find skills that are commonly associated with the candidate's profile
        return Skill::query()->whereNotIn('id', $currentSkillIds)
            ->where(function ($query) use ($jobTitles, $educationFields) {
                // Skills related to job titles
                foreach ($jobTitles as $title) {
                    if ($title) {
                        $query->orWhere('name', 'like', "%{$title}%");
                    }
                }

                // Skills related to education fields
                foreach ($educationFields as $field) {
                    if ($field) {
                        $query->orWhere('name', 'like', "%{$field}%");
                    }
                }
            })
            ->orWhereIn('id', function ($query) use ($currentSkillIds) {
                // Skills that are commonly paired with the candidate's current skills
                $query->select('skill_id')
                    ->from('job_skills')
                    ->whereIn('job_id', function ($subQuery) use ($currentSkillIds) {
                        $subQuery->select('job_id')
                            ->from('job_skills')
                            ->whereIn('skill_id', $currentSkillIds);
                    })
                    ->whereNotIn('skill_id', $currentSkillIds)
                    ->groupBy('skill_id')
                    ->orderByRaw('COUNT(*) DESC');
            })
            ->take($limit)
            ->get();
    }
}
