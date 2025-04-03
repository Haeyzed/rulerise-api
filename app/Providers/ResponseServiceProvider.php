<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Helpers\TranslationHelper;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerSuccessResponse();
        $this->registerPaginatedSuccessResponse();
        $this->registerErrorResponse();
        $this->registerValidationErrorResponse();
        $this->registerNotFoundResponse();
        $this->registerUnauthorizedResponse();
        $this->registerForbiddenResponse();
        $this->registerCreatedResponse();
        $this->registerNoContentResponse();
    }

    /**
     * Register success response macro.
     *
     * @return void
     */
    protected function registerSuccessResponse()
    {
        Response::macro('success', function ($data = null, string $message = 'Success', int $statusCode = 200, array $headers = []): JsonResponse {
            return Response::json([
                'success' => true,
                'message' => TranslationHelper::translateMessage($message),
                'data' => $data,
            ], $statusCode, $headers);
        });
    }

    /**
     * Register paginated success response macro.
     *
     * @return void
     */
    protected function registerPaginatedSuccessResponse()
    {
        Response::macro('paginatedSuccess', function (AnonymousResourceCollection $collection, string $message = 'Success', int $statusCode = 200, array $headers = []): JsonResponse {
            $resourceResponse = $collection->response()->getData(true);
            
            return Response::json([
                'success' => true,
                'message' => TranslationHelper::translateMessage($message),
                'data' => $resourceResponse['data'],
                'meta' => [
                    'current_page' => $resourceResponse['meta']['current_page'],
                    'last_page' => $resourceResponse['meta']['last_page'],
                    'per_page' => $resourceResponse['meta']['per_page'],
                    'total' => $resourceResponse['meta']['total'],
                ],
                'links' => $resourceResponse['links'] ?? null,
            ], $statusCode, $headers);
        });
    }

    /**
     * Register error response macro.
     *
     * @return void
     */
    protected function registerErrorResponse()
    {
        Response::macro('error', function (string $message = 'Error', int $statusCode = 400, array $errors = [], array $headers = []): JsonResponse {
            $response = [
                'success' => false,
                'message' => TranslationHelper::translateMessage($message),
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return Response::json($response, $statusCode, $headers);
        });
    }

    /**
     * Register validation error response macro.
     *
     * @return void
     */
    protected function registerValidationErrorResponse()
    {
        Response::macro('validationError', function (array $errors, string $message = 'Validation Error', array $headers = []): JsonResponse {
            return Response::json([
                'success' => false,
                'message' => TranslationHelper::translateMessage($message),
                'errors' => $errors,
            ], 422, $headers);
        });
    }

    /**
     * Register not found response macro.
     *
     * @return void
     */
    protected function registerNotFoundResponse()
    {
        Response::macro('notFound', function (string $message = 'Resource not found', array $headers = []): JsonResponse {
            return Response::json([
                'success' => false,
                'message' => TranslationHelper::translateMessage($message),
            ], 404, $headers);
        });
    }

    /**
     * Register unauthorized response macro.
     *
     * @return void
     */
    protected function registerUnauthorizedResponse()
    {
        Response::macro('unauthorized', function (string $message = 'Unauthorized', array $headers = []): JsonResponse {
            return Response::json([
                'success' => false,
                'message' => TranslationHelper::translateMessage($message),
            ], 401, $headers);
        });
    }

    /**
     * Register forbidden response macro.
     *
     * @return void
     */
    protected function registerForbiddenResponse()
    {
        Response::macro('forbidden', function (string $message = 'Forbidden', array $headers = []): JsonResponse {
            return Response::json([
                'success' => false,
                'message' => TranslationHelper::translateMessage($message),
            ], 403, $headers);
        });
    }

    /**
     * Register created response macro.
     *
     * @return void
     */
    protected function registerCreatedResponse()
    {
        Response::macro('created', function ($data = null, string $message = 'Resource created successfully', array $headers = []): JsonResponse {
            return Response::json([
                'success' => true,
                'message' => TranslationHelper::translateMessage($message),
                'data' => $data,
            ], 201, $headers);
        });
    }

    /**
     * Register no content response macro.
     *
     * @return void
     */
    protected function registerNoContentResponse()
    {
        Response::macro('noContent', function (array $headers = []): JsonResponse {
            return Response::json(null, 204, $headers);
        });
    }
}

