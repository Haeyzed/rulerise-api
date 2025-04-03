<?php

namespace App\Notifications;

use App\Models\JobAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class JobAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The job alert instance.
     *
     * @var JobAlert
     */
    protected $jobAlert;

    /**
     * The matching jobs.
     *
     * @var Collection
     */
    protected $matchingJobs;

    /**
     * Create a new notification instance.
     *
     * @param JobAlert $jobAlert
     * @param Collection $matchingJobs
     * @return void
     */
    public function __construct(JobAlert $jobAlert, Collection $matchingJobs)
    {
        $this->jobAlert = $jobAlert;
        $this->matchingJobs = $matchingJobs;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Jobs Matching Your Alert')
            ->markdown('emails.jobs.job-alert', [
                'user' => $notifiable,
                'jobAlert' => $this->jobAlert,
                'matchingJobs' => $this->matchingJobs,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'job_alert',
            'job_alert_id' => $this->jobAlert->id,
            'job_alert_name' => $this->jobAlert->name,
            'matching_jobs_count' => $this->matchingJobs->count(),
            'matching_job_ids' => $this->matchingJobs->pluck('id')->toArray(),
            'message' => "We found {$this->matchingJobs->count()} new jobs matching your '{$this->jobAlert->name}' alert.",
            'importance' => 'medium',
        ];
    }
}

