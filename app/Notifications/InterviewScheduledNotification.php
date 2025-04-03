<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The interview instance.
     *
     * @var Interview
     */
    protected $interview;

    /**
     * Create a new notification instance.
     *
     * @param Interview $interview
     * @return void
     */
    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
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
        $jobApplication = $this->interview->jobApplication;
        $job = $jobApplication->job;
        $company = $job->company;
        
        return (new MailMessage)
            ->subject('Interview Scheduled')
            ->markdown('emails.interviews.scheduled', [
                'user' => $notifiable,
                'interview' => $this->interview,
                'jobApplication' => $jobApplication,
                'job' => $job,
                'company' => $company,
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
        $jobApplication = $this->interview->jobApplication;
        $job = $jobApplication->job;
        $company = $job->company;
        
        return [
            'type' => 'interview_scheduled',
            'interview_id' => $this->interview->id,
            'job_application_id' => $jobApplication->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'company_id' => $company->id,
            'company_name' => $company->name,
            'interview_date' => $this->interview->scheduled_at->format('Y-m-d H:i:s'),
            'interview_type' => $this->interview->type,
            'message' => "An interview has been scheduled for {$job->title} at {$company->name} on {$this->interview->scheduled_at->format('F j, Y \a\t g:i A')}.",
            'importance' => 'high',
        ];
    }
}

