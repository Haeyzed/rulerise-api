<?php

namespace App\Services;

use App\Models\User;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\CandidateJobPool;
use App\Models\Interview;
use App\Models\Message;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\ApplicationStatusChangedNotification;
use App\Notifications\NewJobApplication;
use App\Notifications\InterviewScheduled;
use App\Notifications\NewMessage;
use App\Notifications\JobAlertNotification;
use App\Notifications\CandidateJobPoolStatusChanged;
use App\Notifications\CandidateJobPoolStatusChangedNotification;
use App\Notifications\InterviewScheduledNotification;
use App\Notifications\NewJobApplicationNotification;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a notification for a job application status change.
     *
     * @param JobApplication $application
     * @param string $oldStatus
     * @param string $newStatus
     * @param string|null $notes
     * @return void
     */
    public function sendApplicationStatusChangedNotification(JobApplication $application, string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        $candidate = $application->candidate->user;

        if ($candidate) {
            Notification::send($candidate, new ApplicationStatusChangedNotification($application, $oldStatus, $newStatus, $notes));
        }
    }

    /**
     * Send a notification for a new job application.
     *
     * @param JobApplication $application
     * @return void
     */
    public function sendNewJobApplicationNotification(JobApplication $application): void
    {
        $job = $application->job;
        $employer = $job->company->user;

        if ($employer) {
            Notification::send($employer, new NewJobApplicationNotification($application));
        }
    }

    /**
     * Send a notification for a scheduled interview.
     *
     * @param Interview $interview
     * @return void
     */
    public function sendInterviewScheduledNotification(Interview $interview): void
    {
        $application = $interview->application;
        $candidate = $application->candidate->user;

        if ($candidate) {
            Notification::send($candidate, new InterviewScheduledNotification($interview));
        }
    }

    /**
     * Send a notification for a new message.
     *
     * @param Message $message
     * @return void
     */
    public function sendNewMessageNotification(Message $message): void
    {
        $receiver = $message->receiver;

        if ($receiver) {
            Notification::send($receiver, new NewMessageNotification($message));
        }
    }

    /**
     * Send a job alert notification.
     *
     * @param User $user
     * @param array $jobs
     * @param array $alertData
     * @return void
     */
    public function sendJobAlertNotification(User $user, array $jobs, array $alertData): void
    {
        Notification::send($user, new JobAlertNotification($jobs, $alertData));
    }

    /**
     * Send a notification for a candidate job pool status change.
     *
     * @param CandidateJobPool $candidateJobPool
     * @param string $oldStatus
     * @param string $newStatus
     * @param string|null $notes
     * @return void
     */
    public function sendCandidateJobPoolStatusChangedNotification(CandidateJobPool $candidateJobPool, string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        $candidate = $candidateJobPool->candidate->user;

        if ($candidate) {
            Notification::send($candidate, new CandidateJobPoolStatusChangedNotification($candidateJobPool, $oldStatus, $newStatus, $notes));
        }
    }

    /**
     * Send a notification to multiple users.
     *
     * @param array $userIds
     * @param mixed $notification
     * @return void
     */
    public function sendNotificationToUsers(array $userIds, mixed $notification): void
    {
        $users = User::query()->whereIn('id', $userIds)->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, $notification);
        }
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param User $user
     * @return void
     */
    public function markAllNotificationsAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Mark a specific notification as read.
     *
     * @param User $user
     * @param string $notificationId
     * @return void
     */
    public function markNotificationAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Delete a specific notification.
     *
     * @param User $user
     * @param string $notificationId
     * @return void
     */
    public function deleteNotification(User $user, string $notificationId): void
    {
        $user->notifications()->where('id', $notificationId)->delete();
    }

    /**
     * Delete all notifications for a user.
     *
     * @param User $user
     * @return void
     */
    public function deleteAllNotifications(User $user): void
    {
        $user->notifications()->delete();
    }
}

