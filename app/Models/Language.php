<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Language extends \Nnjeim\World\Models\Language
{
//    use HasFactory;

//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array<int, string>
//     */
//    protected $fillable = [
//        'name',
//        'code',
//        'native_name',
//        'is_active',
//    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the candidate languages for the language.
     *
     * @return HasMany<CandidateLanguage>
     */
    public function candidateLanguages(): HasMany
    {
        return $this->hasMany(CandidateLanguage::class);
    }

    /**
     * Scope a query to only include active languages.
     *
     * @param Builder<Language> $query
     * @return Builder<Language>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

