<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    abstract public function viewAny(User $user): bool;

    /**
     * Determine whether the user can view the model.
     */
    abstract public function view(User $user, Model $model): bool;

    /**
     * Determine whether the user can create models.
     */
    abstract public function create(User $user): bool;

    /**
     * Determine whether the user can update the model.
     */
    abstract public function update(User $user, Model $model): bool;

    /**
     * Determine whether the user can delete the model.
     */
    abstract public function delete(User $user, Model $model): bool;

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Model $model): bool
    {
        return $user->hasPermissionTo('restore ' . $this->getModelName());
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return $user->hasPermissionTo('force delete ' . $this->getModelName());
    }

    /**
     * Get the model name for permission checks.
     */
    abstract protected function getModelName(): string;
}

