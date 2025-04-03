<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class JobPoolPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Employers can view job pools
        if ($user->role === 'employer') {
            return true;
        }

        return $user->hasPermissionTo('view job pools');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Employers can view their own job pools
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('view job pools');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Employers can create job pools
        if ($user->role === 'employer' && $user->companies->count() > 0) {
            return true;
        }

        return $user->hasPermissionTo('create job pools');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Employers can update their own job pools
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('update job pools');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Employers can delete their own job pools
        if ($user->role === 'employer') {
            $companyIds = $user->companies->pluck('id')->toArray();
            if (in_array($model->company_id, $companyIds)) {
                return true;
            }
        }

        return $user->hasPermissionTo('delete job pools');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'job pools';
    }
}

