<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateCertification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'name',
        'issuing_organization',
        'issue_date',
        'expiration_date',
        'credential_id',
        'credential_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
    ];

    /**
     * Get the candidate that owns the certification.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the is expired attribute.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    /**
     * Get the validity period attribute.
     */
    public function getValidityPeriodAttribute(): ?string
    {
        if (!$this->issue_date) {
            return null;
        }
        
        $issueDate = $this->issue_date->format('M Y');
        $expirationDate = $this->expiration_date ? $this->expiration_date->format('M Y') : 'No Expiration';
        
        return $issueDate . ' - ' . $expirationDate;
    }
}

