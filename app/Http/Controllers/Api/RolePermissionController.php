<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Requests\Permission\PermissionRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    /**
     * The role permission service instance.
     *
     * @var RolePermissionService
     */
    protected $rolePermissionService;

    /**
     * Create a new controller instance.
     *
     * @param RolePermissionService $rolePermissionService
     * @return void
     */
    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Get all roles.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRoles(Request $request): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to view roles');
        }
        
        $roles = $this->rolePermissionService->getRoles($request->all());
        
        return response()->paginatedSuccess(
            RoleResource::collection($roles),
            'Roles retrieved successfully'
        );
    }

    /**
     * Get a specific role.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function getRole(Role $role): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to view roles');
        }
        
        $role->load('permissions');
        
        return response()->success(
            new RoleResource($role),
            'Role retrieved successfully'
        );
    }

    /**
     * Create a new role.
     *
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function createRole(RoleRequest $request): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to create roles');
        }
        
        $role = $this->rolePermissionService->createRole($request->validated());
        
        return response()->success(
            new RoleResource($role),
            'Role created successfully',
            201
        );
    }

    /**
     * Update an existing role.
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return JsonResponse
     */
    public function updateRole(RoleRequest $request, Role $role): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to update roles');
        }
        
        $role = $this->rolePermissionService->updateRole($role, $request->validated());
        
        return response()->success(
            new RoleResource($role),
            'Role updated successfully'
        );
    }

    /**
     * Delete a role.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function deleteRole(Role $role): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to delete roles');
        }
        
        $this->rolePermissionService->deleteRole($role);
        
        return response()->success(null, 'Role deleted successfully');
    }

    /**
     * Get all permissions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPermissions(Request $request): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to view permissions');
        }
        
        $permissions = $this->rolePermissionService->getPermissions($request->all());
        
        return response()->paginatedSuccess(
            PermissionResource::collection($permissions),
            'Permissions retrieved successfully'
        );
    }

    /**
     * Get a specific permission.
     *
     * @param Permission $permission
     * @return JsonResponse
     */
    public function getPermission(Permission $permission): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to view permissions');
        }
        
        $permission->load('roles');
        
        return response()->success(
            new PermissionResource($permission),
            'Permission retrieved successfully'
        );
    }

    /**
     * Create a new permission.
     *
     * @param PermissionRequest $request
     * @return JsonResponse
     */
    public function createPermission(PermissionRequest $request): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to create permissions');
        }
        
        $permission = $this->rolePermissionService->createPermission($request->validated());
        
        return response()->success(
            new PermissionResource($permission),
            'Permission created successfully',
            201
        );
    }

    /**
     * Update an existing permission.
     *
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return JsonResponse
     */
    public function updatePermission(PermissionRequest $request, Permission $permission): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to update permissions');
        }
        
        $permission = $this->rolePermissionService->updatePermission($permission, $request->validated());
        
        return response()->success(
            new PermissionResource($permission),
            'Permission updated successfully'
        );
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission
     * @return JsonResponse
     */
    public function deletePermission(Permission $permission): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to delete permissions');
        }
        
        $this->rolePermissionService->deletePermission($permission);
        
        return response()->success(null, 'Permission deleted successfully');
    }

    /**
     * Get permissions by category.
     *
     * @return JsonResponse
     */
    public function getPermissionsByCategory(): JsonResponse
    {
        if (Gate::denies('manage-permissions')) {
            return response()->forbidden('You do not have permission to view permissions');
        }
        
        $permissionsByCategory = $this->rolePermissionService->getPermissionsByCategory();
        
        return response()->success(
            $permissionsByCategory,
            'Permissions by category retrieved successfully'
        );
    }

    /**
     * Get users by role.
     *
     * @param Request $request
     * @param string $roleName
     * @return JsonResponse
     */
    public function getUsersByRole(Request $request, string $roleName): JsonResponse
    {
        if (Gate::denies('manage-roles')) {
            return response()->forbidden('You do not have permission to view users by role');
        }
        
        $users = $this->rolePermissionService->getUsersByRole($roleName, $request->all());
        
        return response()->paginatedSuccess(
            UserResource::collection($users),
            'Users by role retrieved successfully'
        );
    }

    /**
     * Assign role to user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function assignRoleToUser(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-roles')) {
            return response()->forbidden('You do not have permission to assign roles');
        }
        
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user = $this->rolePermissionService->assignRoleToUser($user, $request->roles);
        
        return response()->success(
            new UserResource($user->load('roles')),
            'Roles assigned successfully'
        );
    }

    /**
     * Remove role from user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function removeRoleFromUser(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-roles')) {
            return response()->forbidden('You do not have permission to remove roles');
        }
        
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user = $this->rolePermissionService->removeRoleFromUser($user, $request->roles);
        
        return response()->success(
            new UserResource($user->load('roles')),
            'Roles removed successfully'
        );
    }

    /**
     * Sync roles for user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function syncUserRoles(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-roles')) {
            return response()->forbidden('You do not have permission to sync roles');
        }
        
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user = $this->rolePermissionService->syncUserRoles($user, $request->roles);
        
        return response()->success(
            new UserResource($user->load('roles')),
            'Roles synced successfully'
        );
    }

    /**
     * Give permissions to user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function givePermissionsToUser(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-permissions')) {
            return response()->forbidden('You do not have permission to give permissions');
        }
        
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        $user = $this->rolePermissionService->givePermissionsToUser($user, $request->permissions);
        
        return response()->success(
            new UserResource($user),
            'Permissions given successfully'
        );
    }

    /**
     * Revoke permissions from user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function revokePermissionsFromUser(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-permissions')) {
            return response()->forbidden('You do not have permission to revoke permissions');
        }
        
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        $user = $this->rolePermissionService->revokePermissionsFromUser($user, $request->permissions);
        
        return response()->success(
            new UserResource($user),
            'Permissions revoked successfully'
        );
    }

    /**
     * Sync permissions for user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function syncUserPermissions(Request $request, User $user): JsonResponse
    {
        if (Gate::denies('assign-permissions')) {
            return response()->forbidden('You do not have permission to sync permissions');
        }
        
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        $user = $this->rolePermissionService->syncUserPermissions($user, $request->permissions);
        
        return response()->success(
            new UserResource($user),
            'Permissions synced successfully'
        );
    }
}

