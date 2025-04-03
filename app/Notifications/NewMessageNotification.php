<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The message instance.
     *
     * @var Message
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
        $sender = $this->message->sender;
        
        return (new MailMessage)
            ->subject('New Message Received')
            ->markdown('emails.messages.new-message', [
                'user' => $notifiable,
                'message' => $this->message,
                'sender' => $sender,
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
        $sender = $this->message->sender;
        
        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'conversation_id' => $this->message->conversation_id,
            'message_preview' => substr($this->message->content, 0, 100),
            'message' => "You have received a new message from {$sender->name}.",
            'importance' => 'medium',
        ];
    }
}

