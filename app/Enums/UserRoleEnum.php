<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case CANDIDATE = 'candidate';
    case EMPLOYER = 'employer';

    /**
     * Get all values as an array.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the label for the enum value.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::CANDIDATE => 'Job Seeker',
            self::EMPLOYER => 'Employer',
        };
    }

    /**
     * Get the color for the enum value.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::ADMIN => '#dc3545', // Red
            self::CANDIDATE => '#28a745', // Green
            self::EMPLOYER => '#007bff', // Blue
        };
    }
}

