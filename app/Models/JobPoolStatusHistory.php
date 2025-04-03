<?php

namespace App\Models;

use App\Enums\ApplicationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPoolStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_pool_status_history';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_job_pool_id',
        'status',
        'notes',
        'changed_by_user_id',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'status' => ApplicationStatusEnum::class,
    ];

    /**
     * Get the candidate job pool that owns the status history.
     *
     * @return BelongsTo<CandidateJobPool, JobPoolStatusHistory>
     */
    public function candidateJobPool(): BelongsTo
    {
        return $this->belongsTo(CandidateJobPool::class, 'candidate_job_pool_id');
    }

    /**
     * Get the user that changed the status.
     *
     * @return BelongsTo<User, JobPoolStatusHistory>
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * Get the status label attribute.
     *
     * @return string|null
     */
    public function getStatusLabelAttribute(): ?string
    {
        return $this->status ? $this->status->label() : null;
    }

    /**
     * Get the status color attribute.
     *
     * @return string|null
     */
    public function getStatusColorAttribute(): ?string
    {
        return $this->status ? $this->status->color() : null;
    }
}

