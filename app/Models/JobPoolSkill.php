<?php

namespace App\Models;

use App\Enums\ImportanceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPoolSkill extends Model
{
    use HasFactory;

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
        'job_pool_id',
        'skill_id',
        'importance',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'importance' => ImportanceEnum::class,
    ];

    /**
     * Get the job pool that owns the skill.
     */
    public function jobPool(): BelongsTo
    {
        return $this->belongsTo(JobPool::class);
    }

    /**
     * Get the skill that owns the job pool skill.
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Get the importance label attribute.
     */
    public function getImportanceLabelAttribute(): ?string
    {
        return $this->importance ? $this->importance->label() : null;
    }

    /**
     * Get the importance color attribute.
     */
    public function getImportanceColorAttribute(): ?string
    {
        return $this->importance ? $this->importance->color() : null;
    }
}

