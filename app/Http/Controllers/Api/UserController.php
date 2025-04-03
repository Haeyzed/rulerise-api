<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'role', 'is_active', 'country', 'email_verified', 'created_from', 
            'created_to', 'sort_by', 'sort_direction', 'trashed'
        ]);
        
        $users = $this->userService->list($filters, $request->input('per_page', 15));
        
        return response()->paginatedSuccess(
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());
        
        return response()->created(
            new UserResource($user),
            'User created successfully'
        );
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified user in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());
        
        return response()->success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);
        
        return response()->success(null, 'User deleted successfully');
    }

    /**
     * Force delete the specified user from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);
        
        $this->userService->forceDelete($user);
        
        return response()->success(null, 'User permanently deleted successfully');
    }

    /**
     * Restore the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);
        
        $this->userService->restore($user);
        
        return response()->success(
            new UserResource($user),
            'User restored successfully'
        );
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changePassword(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $this->userService->changePassword($user, $request->input('password'));
        
        return response()->success(null, 'Password changed successfully');
    }

    /**
     * Change user status.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeStatus(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $request->validate([
            'is_active' => 'required|boolean',
        ]);
        
        $this->userService->changeStatus($user, $request->input('is_active'));
        
        return response()->success(
            new UserResource($user),
            'User status changed successfully'
        );
    }

    /**
     * Verify user email.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function verifyEmail(User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $this->userService->verifyEmail($user);
        
        return response()->success(
            new UserResource($user),
            'User email verified successfully'
        );
    }

    /**
     * Change user role.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $request->validate([
            'role' => 'required|string|in:admin,employer,candidate',
        ]);
        
        $user = $this->userService->changeRole($user, $request->input('role'));
        
        return response()->success(
            new UserResource($user),
            'User role changed successfully'
        );
    }

    /**
     * Get user statistics.
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        
        $statistics = $this->userService->getStatistics();
        
        return response()->success(
            $statistics,
            'User statistics retrieved successfully'
        );
    }

    /**
     * Get user activity.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getUserActivity(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        
        $activity = $this->userService->getUserActivity($user);
        
        return response()->success(
            $activity,
            'User activity retrieved successfully'
        );
    }

    /**
     * Check if email exists.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmailExists(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'exclude_user_id' => 'nullable|integer|exists:users,id',
        ]);
        
        $exists = $this->userService->emailExists(
            $request->input('email'),
            $request->input('exclude_user_id')
        );
        
        return response()->success(
            ['exists' => $exists],
            'Email existence checked successfully'
        );
    }

    /**
     * Get user dashboard data.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getDashboardData(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        
        $dashboardData = $this->userService->getDashboardData($user);
        
        return response()->success(
            $dashboardData,
            'User dashboard data retrieved successfully'
        );
    }
}

