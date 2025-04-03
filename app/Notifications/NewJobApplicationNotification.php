<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The job application instance.
     *
     * @var JobApplication
     */
    protected $jobApplication;

    /**
     * Create a new notification instance.
     *
     * @param JobApplication $jobApplication
     * @return void
     */
    public function __construct(JobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
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
        $candidate = $this->jobApplication->candidate;
        $job = $this->jobApplication->job;
        
        return (new MailMessage)
            ->subject('New Job Application Received')
            ->markdown('emails.applications.new-application', [
                'user' => $notifiable,
                'jobApplication' => $this->jobApplication,
                'candidate' => $candidate,
                'job' => $job,
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
        $candidate = $this->jobApplication->candidate;
        $job = $this->jobApplication->job;
        
        return [
            'type' => 'new_job_application',
            'job_application_id' => $this->jobApplication->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->user->name,
            'message' => "{$candidate->user->name} has applied for the position of {$job->title}.",
            'importance' => 'medium',
        ];
    }
}

