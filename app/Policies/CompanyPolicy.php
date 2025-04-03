<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Companies are public information
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        return true; // Companies are public information
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Employers can create companies
        if ($user->role === 'employer') {
            return true;
        }

        return $user->hasPermissionTo('create companies');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Employers can update their own companies
        if ($user->role === 'employer' && $model->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('update companies');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Employers can delete their own companies
        if ($user->role === 'employer' && $model->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('delete companies');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'companies';
    }
}

