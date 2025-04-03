<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class JobApplicationPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Candidates can view their own applications
        // Employers can view applications for their jobs
        if (in_array($user->role, ['candidate', 'employer'])) {
            return true;
        }

        return $user->hasPermissionTo('view job applications');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Candidates can view their own applications
        if ($user->role === 'candidate' && $model->candidate_profile->user_id === $user->id) {
            return true;
        }

        // Employers can view applications for their jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->job->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('view job applications');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Candidates can create applications
        if ($user->role === 'candidate' && $user->candidateProfile) {
            return true;
        }

        return $user->hasPermissionTo('create job applications');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Candidates can update their own applications
        if ($user->role === 'candidate' && $model->candidate_profile->user_id === $user->id) {
            return true;
        }

        // Employers can update applications for their jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->job->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('update job applications');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Candidates can delete their own applications
        if ($user->role === 'candidate' && $model->candidate_profile->user_id === $user->id) {
            return true;
        }

        // Employers can delete applications for their jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->job->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('delete job applications');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'job applications';
    }
}

