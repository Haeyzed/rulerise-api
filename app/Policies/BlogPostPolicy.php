<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BlogPostPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Blog posts are public
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Published blog posts can be viewed by anyone
        if ($model->is_published) {
            return true;
        }

        // Authors can view their own unpublished posts
        if ($model->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('view blog posts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Authors can create blog posts
        if ($user->hasRole('author')) {
            return true;
        }

        return $user->hasPermissionTo('create blog posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Authors can update their own posts
        if ($model->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('update blog posts');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Authors can delete their own posts
        if ($model->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('delete blog posts');
    }

    /**
     * Get the model name for permission checks.
     */
    protected function getModelName(): string
    {
        return 'blog posts';
    }
}

