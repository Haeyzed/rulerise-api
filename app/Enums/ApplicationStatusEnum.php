<?php

namespace App\Enums;

enum ApplicationStatusEnum: string
{
    case PENDING = 'pending';
    case REVIEWED = 'reviewed';
    case SHORTLISTED = 'shortlisted';
    case REJECTED = 'rejected';
    case INTERVIEW = 'interview';
    case OFFERED = 'offered';
    case HIRED = 'hired';
    case WITHDRAWN = 'withdrawn';

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
            self::PENDING => 'Pending Review',
            self::REVIEWED => 'Reviewed',
            self::SHORTLISTED => 'Shortlisted',
            self::REJECTED => 'Rejected',
            self::INTERVIEW => 'Interview Scheduled',
            self::OFFERED => 'Job Offered',
            self::HIRED => 'Hired',
            self::WITHDRAWN => 'Withdrawn',
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
            self::PENDING => '#ffc107', // Yellow
            self::REVIEWED => '#17a2b8', // Cyan
            self::SHORTLISTED => '#007bff', // Blue
            self::REJECTED => '#dc3545', // Red
            self::INTERVIEW => '#6f42c1', // Purple
            self::OFFERED => '#fd7e14', // Orange
            self::HIRED => '#28a745', // Green
            self::WITHDRAWN => '#6c757d', // Gray
        };
    }
}

