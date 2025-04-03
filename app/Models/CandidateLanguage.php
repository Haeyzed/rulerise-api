<?php

namespace App\Models;

use App\Enums\LanguageProficiencyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateLanguage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'language_id',
        'proficiency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'proficiency' => LanguageProficiencyEnum::class,
    ];

    /**
     * Get the candidate that owns the language.
     *
     * @return BelongsTo<CandidateProfile, CandidateLanguage>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the language that owns the candidate language.
     *
     * @return BelongsTo<Language, CandidateLanguage>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the proficiency label attribute.
     *
     * @return string|null
     */
    public function getProficiencyLabelAttribute(): ?string
    {
        return $this->proficiency ? $this->proficiency->label() : null;
    }

    /**
     * Get the proficiency color attribute.
     *
     * @return string|null
     */
    public function getProficiencyColorAttribute(): ?string
    {
        return $this->proficiency ? $this->proficiency->color() : null;
    }

    /**
     * Get the language name attribute.
     *
     * @return string|null
     */
    public function getLanguageNameAttribute(): ?string
    {
        return $this->language ? $this->language->name : null;
    }
}

