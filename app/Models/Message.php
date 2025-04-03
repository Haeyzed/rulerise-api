<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'job_id',
        'application_id',
        'subject',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender that owns the message.
     *
     * @return BelongsTo<User, Message>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver that owns the message.
     *
     * @return BelongsTo<User, Message>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the job that owns the message.
     *
     * @return BelongsTo<Job, Message>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the application that owns the message.
     *
     * @return BelongsTo<JobApplication, Message>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            return $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param Builder<Message> $query
     * @return Builder<Message>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages for a specific user.
     *
     * @param Builder<Message> $query
     * @param int $userId
     * @return Builder<Message>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * Scope a query to only include messages received by a user.
     *
     * @param Builder<Message> $query
     * @param int $userId
     * @return Builder<Message>
     */
    public function scopeReceivedBy(Builder $query, int $userId): Builder
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * Scope a query to only include messages sent by a user.
     *
     * @param Builder<Message> $query
     * @param int $userId
     * @return Builder<Message>
     */
    public function scopeSentBy(Builder $query, int $userId): Builder
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * Scope a query to filter by conversation between two users.
     *
     * @param Builder<Message> $query
     * @param int $user1Id
     * @param int $user2Id
     * @return Builder<Message>
     */
    public function scopeConversation(Builder $query, int $user1Id, int $user2Id): Builder
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where(function ($inner) use ($user1Id, $user2Id) {
                $inner->where('sender_id', $user1Id)
                    ->where('receiver_id', $user2Id);
            })->orWhere(function ($inner) use ($user1Id, $user2Id) {
                $inner->where('sender_id', $user2Id)
                    ->where('receiver_id', $user1Id);
            });
        });
    }
}

