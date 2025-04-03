<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected NotificationService $notificationService;

    /**
     * NotificationController constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['read', 'type']);
        $perPage = $request->input('per_page', 15);
        
        $notifications = auth()->user()->notifications()
            ->when(isset($filters['read']), function ($query) use ($filters) {
                if ($filters['read']) {
                    $query->whereNotNull('read_at');
                } else {
                    $query->whereNull('read_at');
                }
            })
            ->when(isset($filters['type']), function ($query) use ($filters) {
                $query->where('type', 'like', '%' . $filters['type'] . '%');
            })
            ->paginate($perPage);
        
        return response()->paginatedSuccess(
            $notifications->items(),
            'Notification retrieved successfully'
        );
    }

    /**
     * Mark notification as read.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $this->notificationService->markNotificationAsRead(auth()->user(), $notification->id);
        
        return response()->success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     *
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllNotificationsAsRead(auth()->user());
        
        return response()->success(null, 'All notifications marked as read');
    }

    /**
     * Delete a notification.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $this->notificationService->deleteNotification(auth()->user(), $notification->id);
        
        return response()->success(null, 'Notification deleted successfully');
    }

    /**
     * Delete all notifications.
     *
     * @return JsonResponse
     */
    public function destroyAll(): JsonResponse
    {
        $this->notificationService->deleteAllNotifications(auth()->user());
        
        return response()->success(null, 'All notifications deleted successfully');
    }

    /**
     * Get unread notifications count.
     *
     * @return JsonResponse
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->unreadNotifications->count();
        
        return response()->success(['count' => $count]);
    }
}

