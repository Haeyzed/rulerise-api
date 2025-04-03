<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

class SettingPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any settings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view settings');
    }

    /**
     * Determine whether the user can view the setting.
     */
    public function view(User $user, Model $model): bool
    {
        return $user->hasPermissionTo('view settings');
    }

    /**
     * Determine whether the user can update the setting.
     */
    public function update(User $user, Model $model): bool
    {
        return $user->hasPermissionTo('manage settings');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'settings';
    }
}

