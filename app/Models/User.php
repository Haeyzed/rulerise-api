<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use App\Services\Storage\StorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Support\Collection;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'profile_picture',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_enabled',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'role' => UserRoleEnum::class,
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
            'name' => $this->full_name,
        ];
    }

    /**
     * Get the candidate profile associated with the user.
     *
     * @return HasOne<CandidateProfile>
     */
    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    /**
     * Get the companies associated with the user.
     *
     * @return HasMany
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the blog posts associated with the user.
     *
     * @return HasMany<BlogPost>
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get the company reviews associated with the user.
     *
     * @return HasMany<CompanyReview>
     */
    public function companyReviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }

    /**
     * Get the job bookmarks associated with the user.
     *
     * @return HasMany<JobBookmark>
     */
    public function jobBookmarks(): HasMany
    {
        return $this->hasMany(JobBookmark::class);
    }

    /**
     * Get the job alerts associated with the user.
     *
     * @return HasMany<JobAlert>
     */
    public function jobAlerts(): HasMany
    {
        return $this->hasMany(JobAlert::class);
    }

    /**
     * Get the sent messages associated with the user.
     *
     * @return HasMany<Message>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the received messages associated with the user.
     *
     * @return HasMany<Message>
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the interviews scheduled by the user.
     *
     * @return HasMany<Interview>
     */
    public function scheduledInterviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'scheduled_by');
    }

    /**
     * Get the full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the profile picture URL attribute.
     *
     * @return string|null
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if (!$this->profile_picture) {
            return null;
        }

        return app(StorageService::class)->url($this->profile_picture);
    }

    /**
     * Scope a query to only include active users.
     *
     * @param Builder<User> $query
     * @return Builder<User>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     *
     * @param Builder<User> $query
     * @param string $role
     * @return Builder<User>
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        return $this->role === $role || parent::hasRole($role);
    }
}

