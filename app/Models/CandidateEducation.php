<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateEducation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'institution',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'grade',
        'activities',
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
     * Get the candidate that owns the education.
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
        $startYear = $this->start_date->format('Y');
        $endYear = $this->is_current ? 'Present' : ($this->end_date ? $this->end_date->format('Y') : 'Present');
        
        return $startYear . ' - ' . $endYear;
    }

    /**
     * Get the formatted degree attribute.
     */
    public function getFormattedDegreeAttribute(): string
    {
        $result = $this->degree;
        
        if ($this->field_of_study) {
            $result .= ' in ' . $this->field_of_study;
        }
        
        return $result;
    }
}

