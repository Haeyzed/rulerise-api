<?php

namespace App\Enums;

enum SupportLevelEnum: string
{
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PRIORITY = 'priority';
    case DEDICATED = 'dedicated';

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
            self::BASIC => 'Basic Support',
            self::STANDARD => 'Standard Support',
            self::PRIORITY => 'Priority Support',
            self::DEDICATED => 'Dedicated Support',
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
            self::BASIC => '#6c757d', // Gray
            self::STANDARD => '#17a2b8', // Cyan
            self::PRIORITY => '#fd7e14', // Orange
            self::DEDICATED => '#9c27b0', // Purple
        };
    }

    /**
     * Get the description for the enum value.
     *
     * @return string
     */
    public function description(): string
    {
        return match($this) {
            self::BASIC => 'Email support with response within 48 hours',
            self::STANDARD => 'Email and chat support with response within 24 hours',
            self::PRIORITY => 'Priority email, chat, and phone support with response within 12 hours',
            self::DEDICATED => 'Dedicated account manager and 24/7 support',
        };
    }
}