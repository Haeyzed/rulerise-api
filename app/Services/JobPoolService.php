<?php

namespace App\Services;

use App\Models\JobPool;
use App\Models\CandidateJobPool;
use App\Models\JobPoolSkill;
use App\Models\CandidateProfile;
use App\Enums\ApplicationStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobPoolService
{
    /**
     * List job pools based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return JobPool::query()
            ->with(['company', 'skills', 'jobs'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['company_id']), function ($query) use ($filters) {
                $query->where('company_id', $filters['company_id']);
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['is_public']), function ($query) use ($filters) {
                $query->where('is_public', $filters['is_public']);
            })
            ->when(isset($filters['department']), function ($query) use ($filters) {
                $query->where('department', $filters['department']);
            })
            ->when(isset($filters['location']), function ($query) use ($filters) {
                $query->where('location', 'like', "%{$filters['location']}%");
            })
            ->when(isset($filters['start_date_from']), function ($query) use ($filters) {
                $query->where('start_date', '>=', $filters['start_date_from']);
            })
            ->when(isset($filters['start_date_to']), function ($query) use ($filters) {
                $query->where('start_date', '<=', $filters['start_date_to']);
            })
            ->when(isset($filters['end_date_from']), function ($query) use ($filters) {
                $query->where('end_date', '>=', $filters['end_date_from']);
            })
            ->when(isset($filters['end_date_to']), function ($query) use ($filters) {
                $query->where('end_date', '<=', $filters['end_date_to']);
            })
            ->when(isset($filters['skill_ids']) && is_array($filters['skill_ids']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($q) use ($filters) {
                    $q->whereIn('skill_id', $filters['skill_ids']);
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
     * Create a new job pool.
     *
     * @param array $data
     * @return JobPool
     */
    public function create(array $data): JobPool
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['name'], $data['company_id']);

            // Create job pool
            $jobPool = JobPool::query()->create($data);

            // Attach skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    $jobPool->jobPoolSkills()->create([
                        'skill_id' => $skill['skill_id'],
                        'importance' => $skill['importance'] ?? 'required',
                    ]);
                }
            }

            // Attach jobs if provided
            if (isset($data['job_ids']) && is_array($data['job_ids'])) {
                $jobPool->jobs()->attach($data['job_ids']);
            }

            return $jobPool;
        });
    }

    /**
     * Update an existing job pool.
     *
     * @param JobPool $jobPool
     * @param array $data
     * @return JobPool
     */
    public function update(JobPool $jobPool, array $data): JobPool
    {
        return DB::transaction(function () use ($jobPool, $data) {
            // Generate slug if name is changed
            if (isset($data['name']) && $data['name'] !== $jobPool->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $jobPool->company_id, $jobPool->id);
            }

            // Update job pool
            $jobPool->update($data);

            // Update skills if provided
            if (isset($data['skills']) && is_array($data['skills'])) {
                // Delete existing skills
                $jobPool->jobPoolSkills()->delete();

                // Add new skills
                foreach ($data['skills'] as $skill) {
                    $jobPool->jobPoolSkills()->create([
                        'skill_id' => $skill['skill_id'],
                        'importance' => $skill['importance'] ?? 'required',
                    ]);
                }
            }

            // Update jobs if provided
            if (isset($data['job_ids']) && is_array($data['job_ids'])) {
                $jobPool->jobs()->sync($data['job_ids']);
            }

            return $jobPool;
        });
    }

    /**
     * Delete a job pool.
     *
     * @param JobPool $jobPool
     * @return bool
     */
    public function delete(JobPool $jobPool): bool
    {
        return $jobPool->delete();
    }

    /**
     * Force delete a job pool.
     *
     * @param JobPool $jobPool
     * @return bool
     */
    public function forceDelete(JobPool $jobPool): bool
    {
        return DB::transaction(function () use ($jobPool) {
            // Delete related records
            $jobPool->jobPoolSkills()->delete();
            $jobPool->jobs()->detach();
            $jobPool->candidateJobPools()->delete();

            return $jobPool->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted job pool.
     *
     * @param JobPool $jobPool
     * @return bool
     */
    public function restore(JobPool $jobPool): bool
    {
        return $jobPool->restore();
    }

    /**
     * Add a candidate to a job pool.
     *
     * @param JobPool $jobPool
     * @param int $candidateId
     * @param array $data
     * @return CandidateJobPool
     */
    public function addCandidate(JobPool $jobPool, int $candidateId, array $data = []): CandidateJobPool
    {
        return DB::transaction(function () use ($jobPool, $candidateId, $data) {
            $status = $data['status'] ?? ApplicationStatusEnum::PENDING->value;
            $notes = $data['notes'] ?? null;
            $addedByUserId = $data['added_by_user_id'] ?? null;

            // Create candidate job pool record
            $candidateJobPool = $jobPool->candidateJobPools()->create([
                'candidate_id' => $candidateId,
                'status' => $status,
                'notes' => $notes,
                'added_by_user_id' => $addedByUserId,
            ]);

            // Create status history record
            $candidateJobPool->statusHistory()->create([
                'status' => $status,
                'notes' => $notes,
                'changed_by_user_id' => $addedByUserId,
                'created_at' => now(),
            ]);

            return $candidateJobPool;
        });
    }

    /**
     * Update candidate status in a job pool.
     *
     * @param CandidateJobPool $candidateJobPool
     * @param string $status
     * @param string|null $notes
     * @param int|null $changedByUserId
     * @return bool
     */
    public function updateCandidateStatus(CandidateJobPool $candidateJobPool, string $status, ?string $notes = null, ?int $changedByUserId = null): bool
    {
        return $candidateJobPool->updateStatus($status, $notes, $changedByUserId);
    }

    /**
     * Remove a candidate from a job pool.
     *
     * @param JobPool $jobPool
     * @param int $candidateId
     * @return bool
     */
    public function removeCandidate(JobPool $jobPool, int $candidateId): bool
    {
        return $jobPool->candidateJobPools()->where('candidate_id', $candidateId)->delete();
    }

    /**
     * Get recommended candidates for a job pool.
     *
     * @param JobPool $jobPool
     * @param int $limit
     * @return Collection
     */
    public function getRecommendedCandidates(JobPool $jobPool, int $limit = 10): Collection
    {
        // Get job pool skills
        $jobPoolSkills = $jobPool->jobPoolSkills()->with('skill')->get();
        $requiredSkillIds = $jobPoolSkills->where('importance', 'required')->pluck('skill_id')->toArray();
        $preferredSkillIds = $jobPoolSkills->where('importance', 'preferred')->pluck('skill_id')->toArray();
        $bonusSkillIds = $jobPoolSkills->where('importance', 'bonus')->pluck('skill_id')->toArray();

        // Get all skill IDs
        $allSkillIds = array_merge($requiredSkillIds, $preferredSkillIds, $bonusSkillIds);

        // Get candidates that are not already in the job pool
        $existingCandidateIds = $jobPool->candidateJobPools()->pluck('candidate_id')->toArray();

        $query = CandidateProfile::query()
            ->with(['user', 'skills.skill', 'educationLevel'])
            ->where('is_public', true)
            ->where('is_available', true)
            ->whereNotIn('id', $existingCandidateIds);

        // If there are required skills, filter by them
        if (!empty($requiredSkillIds)) {
            foreach ($requiredSkillIds as $skillId) {
                $query->whereHas('skills', function ($q) use ($skillId) {
                    $q->where('skill_id', $skillId);
                });
            }
        }

        // Calculate match score
        $query->withCount(['skills as required_skills_count' => function ($q) use ($requiredSkillIds) {
            if (!empty($requiredSkillIds)) {
                $q->whereIn('skill_id', $requiredSkillIds);
            }
        }]);

        $query->withCount(['skills as preferred_skills_count' => function ($q) use ($preferredSkillIds) {
            if (!empty($preferredSkillIds)) {
                $q->whereIn('skill_id', $preferredSkillIds);
            }
        }]);

        $query->withCount(['skills as bonus_skills_count' => function ($q) use ($bonusSkillIds) {
            if (!empty($bonusSkillIds)) {
                $q->whereIn('skill_id', $bonusSkillIds);
            }
        }]);

        // Order by match score
        $query->orderByRaw('(required_skills_count * 3 + preferred_skills_count * 2 + bonus_skills_count) DESC');

        return $query->take($limit)->get();
    }

    /**
     * Get job pool statistics.
     *
     * @param JobPool $jobPool
     * @return array
     */
    public function getStatistics(JobPool $jobPool): array
    {
        $candidateJobPools = $jobPool->candidateJobPools()->with('candidate.user')->get();

        return [
            'total_candidates' => $candidateJobPools->count(),
            'by_status' => [
                'pending' => $candidateJobPools->where('status', ApplicationStatusEnum::PENDING->value)->count(),
                'reviewed' => $candidateJobPools->where('status', ApplicationStatusEnum::REVIEWED->value)->count(),
                'shortlisted' => $candidateJobPools->where('status', ApplicationStatusEnum::SHORTLISTED->value)->count(),
                'rejected' => $candidateJobPools->where('status', ApplicationStatusEnum::REJECTED->value)->count(),
                'interview' => $candidateJobPools->where('status', ApplicationStatusEnum::INTERVIEW->value)->count(),
                'offered' => $candidateJobPools->where('status', ApplicationStatusEnum::OFFERED->value)->count(),
                'hired' => $candidateJobPools->where('status', ApplicationStatusEnum::HIRED->value)->count(),
                'withdrawn' => $candidateJobPools->where('status', ApplicationStatusEnum::WITHDRAWN->value)->count(),
            ],
            'total_jobs' => $jobPool->jobs()->count(),
            'active_jobs' => $jobPool->jobs()->where('is_active', true)->count(),
            'total_skills' => $jobPool->skills()->count(),
            'required_skills' => $jobPool->jobPoolSkills()->where('importance', 'required')->count(),
            'preferred_skills' => $jobPool->jobPoolSkills()->where('importance', 'preferred')->count(),
            'bonus_skills' => $jobPool->jobPoolSkills()->where('importance', 'bonus')->count(),
            'progress' => [
                'target' => $jobPool->target_hiring_count,
                'current' => $jobPool->current_hiring_count,
                'percentage' => $jobPool->progress_percentage,
            ],
            'recent_activity' => $jobPool->candidateStatusHistory()
                ->with(['candidateJobPool.candidate.user', 'changedByUser'])
                ->latest('created_at')
                ->take(10)
                ->get(),
        ];
    }

    /**
     * Generate a unique slug for a job pool.
     *
     * @param string $name
     * @param int $companyId
     * @param int|null $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $name, int $companyId, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $count = JobPool::query()->where('company_id', $companyId)
            ->where('slug', 'like', "{$slug}%")
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}

