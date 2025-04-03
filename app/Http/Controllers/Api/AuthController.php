<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\CandidateRegisterRequest;
use App\Http\Requests\EmployerRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\CandidateService;
use App\Services\CompanyService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * The auth service instance.
     *
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * The user service instance.
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Create a new controller instance.
     *
     * @param AuthService $authService
     * @param UserService $userService
     * @return void
     */
    public function __construct(
        AuthService $authService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login', 'register', 'registerCandidate', 'registerEmployer', 'forgotPassword', 'resetPasswordWithOTP', 'verifyEmail']),
        ];
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     token: string,
     *     token_type: string,
     *     expires_in: string,
     *     data: array{
     *         user: UserResource
     *     }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        // Load the appropriate relationships based on user role
        if ($user->role->value === UserRoleEnum::CANDIDATE->value) {
            $user->load('candidateProfile','roles','permissions');
        } elseif ($user->role->value === UserRoleEnum::EMPLOYER->value) {
            $user->load(['companies','roles','permissions']);
        }

        $result = $this->authService->login([
            'email' => $request->email,
            'password' => $request->password
        ]);

        return response()->created([
            'user' => new UserResource($user),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'User registered successfully');
    }

    /**
     * Register a new candidate (jobseeker).
     *
     * @param CandidateRegisterRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     token: string,
     *     token_type: string,
     *     expires_in: string,
     *     data: array{
     *         user: UserResource
     *     }
     * }
     */
    public function registerCandidate(CandidateRegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerCandidate($request->validated());

        return response()->created([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'Candidate registered successfully');
    }

    /**
     * Register a new employer.
     *
     * @param EmployerRegisterRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     token: string,
     *     token_type: string,
     *     expires_in: string,
     *     data: array{
     *         user: UserResource,
     *         company: CompanyResource
     *     }
     * }
     */
    public function registerEmployer(EmployerRegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerEmployer($request->validated());

        return response()->created([
            'user' => new UserResource($result['user']),
            'company' => new CompanyResource($result['company']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'Employer registered successfully');
    }

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @response array{
     *     status: boolean,
     *     message: string,
     *     token: string,
     *     token_type: string,
     *     expires_in: string,
     *     data: array{
     *         user: UserResource
     *     }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->only(['email', 'password']));

        if (!$result) {
            return response()->unauthorized('Invalid credentials');
        }

        // Load the appropriate relationships based on user role
        if ($result['user']->role->value === UserRoleEnum::CANDIDATE->value) {
            $result['user']->load('candidateProfile','roles','permissions');
        } elseif ($result['user']->role->value === UserRoleEnum::EMPLOYER->value) {
            $result['user']->load(['companies','roles','permissions']);
        }

        return response()->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'User logged in successfully');
    }

    /**
     * Logout a user.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->success(null, 'User logged out successfully');
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      token: string,
     *      token_type: string,
     *      expires_in: string
     *  }
     */
    public function refresh(): JsonResponse
    {
        $result = $this->authService->refreshToken();

        if (!$result) {
            return response()->unauthorized('Unable to refresh token');
        }

        return response()->success([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'Token refreshed successfully');
    }

    /**
     * Get authenticated user profile.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          user: UserResource
     *      }
     *  }
     */
    public function profile(): JsonResponse
    {
        $profile = $this->authService->getProfile(auth()->user());

        return response()->success($profile, 'User profile retrieved successfully');
    }

    /**
     * Update user profile.
     *
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *      data: array{
     *          user: UserResource
     *      }
     *  }
     */
    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile(auth()->user(), $request->validated());

        return response()->success(
            new UserResource($user),
            'Profile updated successfully'
        );
    }

    /**
     * Change user password.
     *
     * @param PasswordUpdateRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string,
     *  }
     */
    public function changePassword(PasswordUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->authService->changePassword(
            auth()->user(),
            $data['current_password'],
            $data['password']
        );

        if (!$result) {
            return response()->error('Current password is incorrect', 422);
        }

        return response()->success(null, 'Password changed successfully');
    }

    /**
     * Send password reset link.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            /**
             * The email address of the user.
             *
             * @var string $email
             * @example "john.doe@example.com"
             */
            'email' => 'required|email',
        ]);

        $status = $this->authService->sendPasswordResetLink($request->email);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->success(null, 'Password reset link sent to your email');
        }

        return response()->error('Unable to send password reset link', 400);
    }

    /**
     * Reset password.
     *
     * @param PasswordResetRequest $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return response()->success(null, 'Password has been reset successfully');
        }

        return response()->error('Unable to reset password', 400);
    }

    /**
     * Verify email.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'hash' => 'required|string',
        ]);

        $result = $this->authService->verifyEmail($request->id, $request->hash);

        if ($result) {
            return response()->success(null, 'Email verified successfully');
        }

        return response()->error('Invalid verification link', 400);
    }

    /**
     * Resend verification email.
     *
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function resendVerificationEmail(): JsonResponse
    {
        $result = $this->authService->resendVerificationEmail(auth()->user());

        if ($result) {
            return response()->success(null, 'Verification email sent successfully');
        }

        return response()->error('Email already verified', 400);
    }

    /**
     * Deactivate account.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function deactivateAccount(Request $request): JsonResponse
    {
        $request->validate([
            /**
             * The password for the user account.
             *
             * @var string $password
             * @example "SecureP@ssw0rd"
             */
            'password' => ['required','string'],
        ]);

        $result = $this->authService->deactivateAccount(auth()->user(), $request->password);

        if ($result) {
            return response()->success(null, 'Account deactivated successfully');
        }

        return response()->error('Invalid password', 422);
    }

    /**
     * Delete account.
     *
     * @param Request $request
     * @return JsonResponse
     * @response array{
     *      status: boolean,
     *      message: string
     *  }
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            /**
             * The password for the user account.
             *
             * @var string $password
             * @example "SecureP@ssw0rd"
             */
            'password' => ['required','string'],
        ]);

        $result = $this->authService->deleteAccount(auth()->user(), $request->password);

        if ($result) {
            return response()->success(null, 'Account deleted successfully');
        }

        return response()->error('Invalid password', 422);
    }
}

