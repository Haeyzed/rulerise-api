<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateExperience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'company_name',
        'job_title',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the candidate that owns the experience.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the duration attribute.
     */
    public function getDurationAttribute(): string
    {
        $startDate = $this->start_date->format('M Y');
        $endDate = $this->is_current ? 'Present' : ($this->end_date ? $this->end_date->format('M Y') : 'Present');
        
        return $startDate . ' - ' . $endDate;
    }

    /**
     * Get the duration in months attribute.
     */
    public function getDurationInMonthsAttribute(): int
    {
        $endDate = $this->is_current ? now() : ($this->end_date ?? now());
        $startDate = $this->start_date;
        
        $years = $endDate->year - $startDate->year;
        $months = $endDate->month - $startDate->month;
        
        return ($years * 12) + $months;
    }

    /**
     * Get the formatted duration attribute.
     */
    public function getFormattedDurationAttribute(): string
    {
        $months = $this->duration_in_months;
        $years = floor($months / 12);
        $remainingMonths = $months % 12;
        
        if ($years > 0 && $remainingMonths > 0) {
            return $years . ' ' . ($years === 1 ? 'year' : 'years') . ' ' . $remainingMonths . ' ' . ($remainingMonths === 1 ? 'month' : 'months');
        } elseif ($years > 0) {
            return $years . ' ' . ($years === 1 ? 'year' : 'years');
        } else {
            return $remainingMonths . ' ' . ($remainingMonths === 1 ? 'month' : 'months');
        }
    }
}

