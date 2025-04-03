<?php

namespace App\Services;

use App\Enums\ApplicationStatusEnum;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationStatusHistory;
use App\Models\User;
use App\Models\Interview;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\NewJobApplication;

class JobApplicationService
{
    /**
     * List job applications based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::query()
            ->with(['job.company', 'candidate.user', 'resume'])
            ->when(isset($filters['job_id']), function ($query) use ($filters) {
                $query->where('job_id', $filters['job_id']);
            })
            ->when(isset($filters['candidate_id']), function ($query) use ($filters) {
                $query->where('candidate_id', $filters['candidate_id']);
            })
            ->when(isset($filters['company_id']), function ($query) use ($filters) {
                $query->whereHas('job', function ($q) use ($filters) {
                    $q->where('company_id', $filters['company_id']);
                });
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['is_viewed']), function ($query) use ($filters) {
                $query->where('is_viewed', $filters['is_viewed']);
            })
            ->when(isset($filters['created_from']), function ($query) use ($filters) {
                $query->where('created_at', '>=', $filters['created_from']);
            })
            ->when(isset($filters['created_to']), function ($query) use ($filters) {
                $query->where('created_at', '<=', $filters['created_to']);
            })
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->whereHas('candidate.user', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('job', function ($sq) use ($search) {
                        $sq->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('job.company', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
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
     * Create a new job application.
     *
     * @param array $data
     * @return JobApplication
     */
    public function create(array $data): JobApplication
    {
        return DB::transaction(function () use ($data) {
            // Create job application
            $application = JobApplication::query()->create($data);

            // Create initial status history
            $application->statusHistory()->create([
                'status' => ApplicationStatusEnum::PENDING->value,
                'notes' => 'Application submitted',
                'changed_by_user_id' => $data['candidate_id'] ?? null,
            ]);

            // Increment job applications count
            $application->job->incrementApplicationsCount();

            // Send notification to employer
            $job = $application->job;
            $employer = $job->company->user;

            if ($employer) {
                Notification::send($employer, new NewJobApplication($application));
            }

            return $application;
        });
    }

    /**
     * Update an existing job application.
     *
     * @param JobApplication $application
     * @param array $data
     * @return JobApplication
     */
    public function update(JobApplication $application, array $data): JobApplication
    {
        return DB::transaction(function () use ($application, $data) {
            // Update job application
            $application->update($data);

            return $application;
        });
    }

    /**
     * Update application status.
     *
     * @param JobApplication $application
     * @param string $status
     * @param string|null $notes
     * @param User|null $user
     * @return JobApplication
     */
    public function updateStatus(JobApplication $application, string $status, ?string $notes = null, ?User $user = null): JobApplication
    {
        return DB::transaction(function () use ($application, $status, $notes, $user) {
            $oldStatus = $application->status;

            // Update application status
            $application->update(['status' => $status]);

            // Create status history
            $statusHistory = $application->statusHistory()->create([
                'status' => $status,
                'notes' => $notes,
                'changed_by_user_id' => $user?->id,
            ]);

            // Send notification to candidate
            $candidate = $application->candidate->user;

            if ($candidate && $oldStatus !== $status) {
                Notification::send($candidate, new ApplicationStatusChanged($application, $oldStatus, $status, $notes));
            }

            return $application;
        });
    }

    /**
     * Mark application as viewed.
     *
     * @param JobApplication $application
     * @return JobApplication
     */
    public function markAsViewed(JobApplication $application): JobApplication
    {
        if (!$application->is_viewed) {
            $application->update([
                'is_viewed' => true,
                'viewed_at' => now(),
            ]);
        }

        return $application;
    }

    /**
     * Delete a job application.
     *
     * @param JobApplication $application
     * @return bool
     */
    public function delete(JobApplication $application): bool
    {
        return DB::transaction(function () use ($application) {
            // Delete status history
            $application->statusHistory()->delete();

            // Delete application
            return $application->delete();
        });
    }

    /**
     * Check if a candidate has already applied to a job.
     *
     * @param int $jobId
     * @param int $candidateId
     * @return bool
     */
    public function hasApplied(int $jobId, int $candidateId): bool
    {
        return JobApplication::query()->where('job_id', $jobId)
            ->where('candidate_id', $candidateId)
            ->exists();
    }

    /**
     * Get application statistics for a job.
     *
     * @param Job $job
     * @return array
     */
    public function getJobApplicationStats(Job $job): array
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'reviewed' => 0,
            'shortlisted' => 0,
            'rejected' => 0,
            'interview' => 0,
            'offered' => 0,
            'hired' => 0,
            'withdrawn' => 0,
        ];

        $applications = $job->applications;
        $stats['total'] = $applications->count();

        foreach (ApplicationStatusEnum::cases() as $status) {
            $statusValue = $status->value;
            $stats[$statusValue] = $applications->where('status', $statusValue)->count();
        }

        // Add more detailed statistics
        $stats['viewed'] = $applications->where('is_viewed', true)->count();
        $stats['unviewed'] = $applications->where('is_viewed', false)->count();

        $stats['by_day'] = $applications
            ->groupBy(function ($application) {
                return $application->created_at->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'count' => $items->count(),
                ];
            })
            ->values();

        $stats['conversion_rate'] = $job->views_count > 0
            ? round(($stats['total'] / $job->views_count) * 100, 2)
            : 0;

        $stats['success_rate'] = $stats['total'] > 0
            ? round((($stats['hired'] + $stats['offered']) / $stats['total']) * 100, 2)
            : 0;

        return $stats;
    }

    /**
     * Schedule an interview for a job application.
     *
     * @param JobApplication $application
     * @param array $data
     * @param User $scheduledBy
     * @return Interview
     */
    public function scheduleInterview(JobApplication $application, array $data, User $scheduledBy): Interview
    {
        return DB::transaction(function () use ($application, $data, $scheduledBy) {
            // Create interview
            $interview = $application->interviews()->create([
                'scheduled_by' => $scheduledBy->id,
                'interview_date' => $data['interview_date'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'location' => $data['location'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
                'is_online' => $data['is_online'] ?? false,
                'notes' => $data['notes'] ?? null,
                'status' => 'scheduled',
            ]);

            // Update application status to interview if not already
            if ($application->status !== ApplicationStatusEnum::INTERVIEW->value) {
                $this->updateStatus(
                    $application,
                    ApplicationStatusEnum::INTERVIEW->value,
                    'Interview scheduled for ' . Carbon::parse($data['interview_date'])->format('M d, Y \a\t h:i A'),
                    $scheduledBy
                );
            }

            return $interview;
        });
    }

    /**
     * Get application timeline.
     *
     * @param JobApplication $application
     * @return Collection
     */
    public function getApplicationTimeline(JobApplication $application): Collection
    {
        $timeline = collect();

        // Add application creation
        $timeline->push([
            'type' => 'application_created',
            'date' => $application->created_at,
            'data' => [
                'application' => $application,
            ],
        ]);

        // Add status changes
        $statusHistory = $application->statusHistory()->with('changedByUser')->get();
        foreach ($statusHistory as $history) {
            $timeline->push([
                'type' => 'status_changed',
                'date' => $history->created_at,
                'data' => [
                    'status' => $history->status,
                    'notes' => $history->notes,
                    'changed_by' => $history->changedByUser,
                ],
            ]);
        }

        // Add interviews
        $interviews = $application->interviews()->with('scheduledBy')->get();
        foreach ($interviews as $interview) {
            $timeline->push([
                'type' => 'interview_scheduled',
                'date' => $interview->created_at,
                'data' => [
                    'interview' => $interview,
                    'scheduled_by' => $interview->scheduledBy,
                ],
            ]);
        }

        // Add messages
        $messages = $application->messages()->with(['sender', 'receiver'])->get();
        foreach ($messages as $message) {
            $timeline->push([
                'type' => 'message_sent',
                'date' => $message->created_at,
                'data' => [
                    'message' => $message,
                    'sender' => $message->sender,
                    'receiver' => $message->receiver,
                ],
            ]);
        }

        // Sort by date
        return $timeline->sortByDesc('date')->values();
    }

    /**
     * Get application analytics for a company.
     *
     * @param int $companyId
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getCompanyApplicationAnalytics(int $companyId, ?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $jobIds = Job::query()->where('company_id', $companyId)->pluck('id')->toArray();

        $applications = JobApplication::query()->whereIn('job_id', $jobIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total_applications' => $applications->count(),
            'by_status' => [
                'pending' => $applications->where('status', ApplicationStatusEnum::PENDING->value)->count(),
                'reviewed' => $applications->where('status', ApplicationStatusEnum::REVIEWED->value)->count(),
                'shortlisted' => $applications->where('status', ApplicationStatusEnum::SHORTLISTED->value)->count(),
                'rejected' => $applications->where('status', ApplicationStatusEnum::REJECTED->value)->count(),
                'interview' => $applications->where('status', ApplicationStatusEnum::INTERVIEW->value)->count(),
                'offered' => $applications->where('status', ApplicationStatusEnum::OFFERED->value)->count(),
                'hired' => $applications->where('status', ApplicationStatusEnum::HIRED->value)->count(),
                'withdrawn' => $applications->where('status', ApplicationStatusEnum::WITHDRAWN->value)->count(),
            ],
            'viewed_rate' => $applications->count() > 0
                ? round(($applications->where('is_viewed', true)->count() / $applications->count()) * 100, 2)
                : 0,
            'success_rate' => $applications->count() > 0
                ? round((($applications->where('status', ApplicationStatusEnum::HIRED->value)->count() +
                          $applications->where('status', ApplicationStatusEnum::OFFERED->value)->count()) /
                         $applications->count()) * 100, 2)
                : 0,
            'by_job' => $applications->groupBy('job_id')
                ->map(function ($items, $jobId) {
                    $job = Job::query()->find($jobId);
                    return [
                        'job_id' => $jobId,
                        'job_title' => $job ? $job->title : 'Unknown',
                        'count' => $items->count(),
                    ];
                })->values(),
            'by_day' => $applications
                ->groupBy(function ($application) {
                    return $application->created_at->format('Y-m-d');
                })
                ->map(function ($items, $date) {
                    return [
                        'date' => $date,
                        'count' => $items->count(),
                    ];
                })
                ->values(),
            'time_to_hire' => $this->calculateTimeToHire($applications),
            'time_to_interview' => $this->calculateTimeToInterview($applications),
        ];
    }

    /**
     * Calculate average time to hire.
     *
     * @param \Illuminate\Support\Collection $applications
     * @return float|null
     */
    private function calculateTimeToHire(\Illuminate\Support\Collection $applications): ?float
    {
        $hiredApplications = $applications->where('status', ApplicationStatusEnum::HIRED->value);

        if ($hiredApplications->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($hiredApplications as $application) {
            $hiredStatus = $application->statusHistory()
                ->where('status', ApplicationStatusEnum::HIRED->value)
                ->first();

            if ($hiredStatus) {
                $totalDays += $application->created_at->diffInDays($hiredStatus->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }

    /**
     * Calculate average time to interview.
     *
     * @param \Illuminate\Support\Collection $applications
     * @return float|null
     */
    private function calculateTimeToInterview(\Illuminate\Support\Collection $applications): ?float
    {
        $interviewApplications = $applications->whereIn('status', [
            ApplicationStatusEnum::INTERVIEW->value,
            ApplicationStatusEnum::OFFERED->value,
            ApplicationStatusEnum::HIRED->value,
        ]);

        if ($interviewApplications->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($interviewApplications as $application) {
            $interviewStatus = $application->statusHistory()
                ->where('status', ApplicationStatusEnum::INTERVIEW->value)
                ->first();

            if ($interviewStatus) {
                $totalDays += $application->created_at->diffInDays($interviewStatus->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }
}

