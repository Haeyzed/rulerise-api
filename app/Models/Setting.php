<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function setValue(string $key, $value): Setting
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get multiple settings at once.
     *
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    public static function getMultiple(array $keys): array
    {
        $settings = static::query()->whereIn('key', $keys)->get();

        $result = [];
        foreach ($keys as $key) {
            $setting = $settings->firstWhere('key', $key);
            $result[$key] = $setting ? $setting->value : null;
        }

        return $result;
    }

    /**
     * Set multiple settings at once.
     *
     * @param array<string, mixed> $settings
     * @return void
     */
    public static function setMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::setValue($key, $value);
        }
    }

    /**
     * Get all public settings.
     *
     * @return array<string, mixed>
     */
    public static function getPublic(): array
    {
        $settings = static::query()->where('is_public', true)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }

    /**
     * Get settings by group.
     *
     * @param string $group
     * @return array<string, mixed>
     */
    public static function getByGroup(string $group): array
    {
        $settings = static::query()->where('group', $group)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }

        return $result;
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}