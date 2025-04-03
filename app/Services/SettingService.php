<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Cache key for settings.
     *
     * @var string
     */
    protected string $cacheKey = 'app_settings';

    /**
     * Cache expiration time in seconds.
     *
     * @var int
     */
    protected int $cacheExpiration = 86400; // 24 hours

    /**
     * Get all settings.
     *
     * @return array
     */
    public function all(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheExpiration, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    /**
     * Get multiple settings at once.
     *
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys): array
    {
        $settings = $this->all();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return $result;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        Setting::setValue($key, $value);
        $this->clearCache();
    }

    /**
     * Set multiple settings at once.
     *
     * @param array $settings
     * @return void
     */
    public function setMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        $this->clearCache();
    }

    /**
     * Delete a setting by key.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        Setting::query()->where('key', $key)->delete();
        $this->clearCache();
    }

    /**
     * Delete multiple settings at once.
     *
     * @param array $keys
     * @return void
     */
    public function deleteMultiple(array $keys): void
    {
        Setting::query()->whereIn('key', $keys)->delete();
        $this->clearCache();
    }

    /**
     * Clear the settings cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Get site settings.
     *
     * @return array
     */
    public function getSiteSettings(): array
    {
        $keys = [
            'site_name',
            'site_tagline',
            'site_description',
            'site_logo',
            'site_favicon',
            'site_email',
            'site_phone',
            'site_address',
            'site_footer_text',
            'social_facebook',
            'social_twitter',
            'social_linkedin',
            'social_instagram',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get SEO settings.
     *
     * @return array
     */
    public function getSeoSettings(): array
    {
        $keys = [
            'seo_title_format',
            'seo_description',
            'seo_keywords',
            'google_analytics_id',
            'google_tag_manager_id',
            'facebook_pixel_id',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get email settings.
     *
     * @return array
     */
    public function getEmailSettings(): array
    {
        $keys = [
            'mail_from_address',
            'mail_from_name',
            'mail_footer_text',
            'mail_logo',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get job settings.
     *
     * @return array
     */
    public function getJobSettings(): array
    {
        $keys = [
            'jobs_per_page',
            'featured_jobs_limit',
            'job_default_expiry_days',
            'enable_job_approval',
            'enable_featured_jobs',
            'enable_urgent_jobs',
            'enable_job_salary',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get candidate settings.
     *
     * @return array
     */
    public function getCandidateSettings(): array
    {
        $keys = [
            'candidates_per_page',
            'featured_candidates_limit',
            'enable_candidate_approval',
            'enable_featured_candidates',
            'enable_candidate_salary',
            'enable_candidate_profile_completion',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get company settings.
     *
     * @return array
     */
    public function getCompanySettings(): array
    {
        $keys = [
            'companies_per_page',
            'featured_companies_limit',
            'enable_company_approval',
            'enable_featured_companies',
            'enable_company_reviews',
        ];

        return $this->getMultiple($keys);
    }

    /**
     * Get blog settings.
     *
     * @return array
     */
    public function getBlogSettings(): array
    {
        $keys = [
            'blog_posts_per_page',
            'enable_blog_comments',
            'enable_blog_author',
            'enable_blog_social_sharing',
        ];

        return $this->getMultiple($keys);
    }
}

