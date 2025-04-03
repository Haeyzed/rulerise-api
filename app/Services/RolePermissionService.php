<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RolePermissionService
{
    /**
     * Get all roles.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%");
            })
            ->when(isset($filters['guard_name']), function ($query) use ($filters) {
                $query->where('guard_name', $filters['guard_name']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->orderBy('name');
            })
            ->paginate($perPage);
    }

    /**
     * Get all permissions.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPermissions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Permission::query()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%");
            })
            ->when(isset($filters['guard_name']), function ($query) use ($filters) {
                $query->where('guard_name', $filters['guard_name']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->orderBy('name');
            })
            ->paginate($perPage);
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
                'description' => $data['description'] ?? null,
            ]);
            
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            
            return $role;
        });
    }

    /**
     * Update an existing role.
     *
     * @param Role $role
     * @param array $data
     * @return Role
     */
    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'description' => $data['description'] ?? $role->description,
            ]);
            
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
            
            return $role;
        });
    }

    /**
     * Delete a role.
     *
     * @param Role $role
     * @return bool
     */
    public function deleteRole(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return Permission
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update an existing permission.
     *
     * @param Permission $permission
     * @param array $data
     * @return Permission
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'] ?? $permission->name,
            'description' => $data['description'] ?? $permission->description,
        ]);
        
        return $permission;
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission
     * @return bool
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function assignRoleToUser(User $user, $roles): User
    {
        $user->assignRole($roles);
        return $user;
    }

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function removeRoleFromUser(User $user, $roles): User
    {
        $user->removeRole($roles);
        return $user;
    }

    /**
     * Sync roles for a user.
     *
     * @param User $user
     * @param array $roles
     * @return User
     */
    public function syncUserRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);
        return $user;
    }

    /**
     * Give permissions to a user.
     *
     * @param User $user
     * @param string|array $permissions
     * @return User
     */
    public function givePermissionsToUser(User $user, $permissions): User
    {
        $user->givePermissionTo($permissions);
        return $user;
    }

    /**
     * Revoke permissions from a user.
     *
     * @param User $user
     * @param string|array $permissions
     * @return User
     */
    public function revokePermissionsFromUser(User $user, $permissions): User
    {
        $user->revokePermissionTo($permissions);
        return $user;
    }

    /**
     * Sync permissions for a user.
     *
     * @param User $user
     * @param array $permissions
     * @return User
     */
    public function syncUserPermissions(User $user, array $permissions): User
    {
        $user->syncPermissions($permissions);
        return $user;
    }

    /**
     * Get users by role.
     *
     * @param string $role
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByRole(string $role, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::role($role)
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Get users by permission.
     *
     * @param string $permission
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByPermission(string $permission, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::permission($permission)
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Get all permissions grouped by category.
     *
     * @return Collection
     */
    public function getPermissionsByCategory(): Collection
    {
        return Permission::all()
            ->groupBy(function ($permission) {
                // Extract category from permission name (e.g., "jobs.create" => "jobs")
                $parts = explode('.', $permission->name);
                return $parts[0];
            });
    }

    /**
     * Check if a user has a specific role.
     *
     * @param User $user
     * @param string|array $roles
     * @param string $guard
     * @return bool
     */
    public function userHasRole(User $user, $roles, string $guard = null): bool
    {
        return $user->hasRole($roles, $guard);
    }

    /**
     * Check if a user has a specific permission.
     *
     * @param User $user
     * @param string|array $permissions
     * @param string $guard
     * @return bool
     */
    public function userHasPermission(User $user, $permissions, string $guard = null): bool
    {
        return $user->hasPermissionTo($permissions, $guard);
    }

    /**
     * Check if a user has any of the given permissions.
     *
     * @param User $user
     * @param array $permissions
     * @return bool
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if a user has all of the given permissions.
     *
     * @param User $user
     * @param array $permissions
     * @return bool
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    /**
     * Get all permissions for a role.
     *
     * @param Role $role
     * @return Collection
     */
    public function getRolePermissions(Role $role): Collection
    {
        return $role->permissions;
    }

    /**
     * Get all roles for a permission.
     *
     * @param Permission $permission
     * @return Collection
     */
    public function getPermissionRoles(Permission $permission): Collection
    {
        return $permission->roles;
    }

    /**
     * Get all roles for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserRoles(User $user): Collection
    {
        return $user->roles;
    }

    /**
     * Get all permissions for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Get direct permissions for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserDirectPermissions(User $user): Collection
    {
        return $user->getDirectPermissions();
    }

    /**
     * Get permissions for a user via roles.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserPermissionsViaRoles(User $user): Collection
    {
        return $user->getPermissionsViaRoles();
    }
}

