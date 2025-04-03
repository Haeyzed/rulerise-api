<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the message.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The user ID of the sender.
             *
             * @var int $sender_id
             * @example 1
             */
            'sender_id' => $this->sender_id,

            /**
             * The sender details.
             *
             * @var array|null $sender
             */
            'sender' => new UserResource($this->whenLoaded('sender')),

            /**
             * The user ID of the receiver.
             *
             * @var int $receiver_id
             * @example 2
             */
            'receiver_id' => $this->receiver_id,

            /**
             * The receiver details.
             *
             * @var array|null $receiver
             */
            'receiver' => new UserResource($this->whenLoaded('receiver')),

            /**
             * The job ID associated with this message.
             *
             * @var int|null $job_id
             * @example 5
             */
            'job_id' => $this->job_id,

            /**
             * The job details.
             *
             * @var array|null $job
             */
            'job' => new JobResource($this->whenLoaded('job')),

            /**
             * The application ID associated with this message.
             *
             * @var int|null $application_id
             * @example 10
             */
            'application_id' => $this->application_id,

            /**
             * The application details.
             *
             * @var array|null $application
             */
            'application' => new JobApplicationResource($this->whenLoaded('application')),

            /**
             * The subject of the message.
             *
             * @var string|null $subject
             * @example "Interview Follow-up"
             */
            'subject' => $this->subject,

            /**
             * The content of the message.
             *
             * @var string $message
             * @example "Thank you for your time during the interview yesterday..."
             */
            'message' => $this->message,

            /**
             * Whether the message has been read.
             *
             * @var bool $is_read
             * @example false
             */
            'is_read' => $this->is_read,

            /**
             * The date and time when the message was read.
             *
             * @var string|null $read_at
             * @example "2023-02-01T10:30:00.000000Z"
             */
            'read_at' => $this->read_at,

            /**
             * The timestamp when the message was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the message was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

