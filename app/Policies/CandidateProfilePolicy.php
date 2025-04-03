<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CandidateProfilePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Employers can view candidate profiles
        if ($user->role === 'employer') {
            return true;
        }

        return $user->hasPermissionTo('view candidate profiles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Candidates can view their own profile
        if ($user->id === $model->user_id) {
            return true;
        }

        // Employers can view candidate profiles
        if ($user->role === 'employer') {
            return true;
        }

        return $user->hasPermissionTo('view candidate profiles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Candidates can create their own profile
        if ($user->role === 'candidate' && !$user->candidateProfile) {
            return true;
        }

        return $user->hasPermissionTo('create candidate profiles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Candidates can update their own profile
        if ($user->id === $model->user_id) {
            return true;
        }

        return $user->hasPermissionTo('update candidate profiles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Candidates can delete their own profile
        if ($user->id === $model->user_id) {
            return true;
        }

        return $user->hasPermissionTo('delete candidate profiles');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'candidate profiles';
    }
}

