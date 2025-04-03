<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The job application instance.
     *
     * @var JobApplication
     */
    protected $jobApplication;

    /**
     * The previous status.
     *
     * @var string
     */
    protected $previousStatus;

    /**
     * Create a new notification instance.
     *
     * @param JobApplication $jobApplication
     * @param string $previousStatus
     * @return void
     */
    public function __construct(JobApplication $jobApplication, string $previousStatus)
    {
        $this->jobApplication = $jobApplication;
        $this->previousStatus = $previousStatus;
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
        $job = $this->jobApplication->job;
        $company = $job->company;
        
        return (new MailMessage)
            ->subject('Your Job Application Status Has Changed')
            ->markdown('emails.applications.status-changed', [
                'user' => $notifiable,
                'jobApplication' => $this->jobApplication,
                'job' => $job,
                'company' => $company,
                'previousStatus' => $this->previousStatus,
                'currentStatus' => $this->jobApplication->status,
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
        $job = $this->jobApplication->job;
        $company = $job->company;
        
        return [
            'type' => 'application_status_changed',
            'job_application_id' => $this->jobApplication->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'company_id' => $company->id,
            'company_name' => $company->name,
            'previous_status' => $this->previousStatus,
            'current_status' => $this->jobApplication->status,
            'message' => "Your application for {$job->title} at {$company->name} has been updated from {$this->previousStatus} to {$this->jobApplication->status}.",
            'importance' => 'high',
        ];
    }
}

