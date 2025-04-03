<?php

namespace App\Notifications;

use App\Models\CandidateJobPool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateJobPoolStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The candidate job pool instance.
     *
     * @var CandidateJobPool
     */
    protected $candidateJobPool;

    /**
     * The previous status.
     *
     * @var string
     */
    protected $previousStatus;

    /**
     * Create a new notification instance.
     *
     * @param CandidateJobPool $candidateJobPool
     * @param string $previousStatus
     * @return void
     */
    public function __construct(CandidateJobPool $candidateJobPool, string $previousStatus)
    {
        $this->candidateJobPool = $candidateJobPool;
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
        $jobPool = $this->candidateJobPool->jobPool;
        $company = $jobPool->company;
        
        return (new MailMessage)
            ->subject('Your Job Pool Status Has Changed')
            ->markdown('emails.job-pools.status-changed', [
                'user' => $notifiable,
                'candidateJobPool' => $this->candidateJobPool,
                'jobPool' => $jobPool,
                'company' => $company,
                'previousStatus' => $this->previousStatus,
                'currentStatus' => $this->candidateJobPool->status,
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
        $jobPool = $this->candidateJobPool->jobPool;
        $company = $jobPool->company;
        
        return [
            'type' => 'job_pool_status_changed',
            'candidate_job_pool_id' => $this->candidateJobPool->id,
            'job_pool_id' => $jobPool->id,
            'job_pool_name' => $jobPool->name,
            'company_id' => $company->id,
            'company_name' => $company->name,
            'previous_status' => $this->previousStatus,
            'current_status' => $this->candidateJobPool->status,
            'message' => "Your status in the {$jobPool->name} talent pool at {$company->name} has been updated from {$this->previousStatus} to {$this->candidateJobPool->status}.",
            'importance' => 'high',
        ];
    }
}

