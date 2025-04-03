<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class JobPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view jobs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Published jobs can be viewed by anyone
        if ($model->is_published) {
            return true;
        }

        // Employers can view their own company's jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('view jobs');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Employers can create jobs
        if ($user->role === 'employer' && $user->companies->count() > 0) {
            return true;
        }

        return $user->hasPermissionTo('create jobs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Employers can update their own company's jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('update jobs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Employers can delete their own company's jobs
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('delete jobs');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'jobs';
    }
}

