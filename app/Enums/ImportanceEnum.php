<?php

namespace App\Enums;

enum ImportanceEnum: string
{
    case REQUIRED = 'required';
    case PREFERRED = 'preferred';
    case BONUS = 'bonus';

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
            self::REQUIRED => 'Required',
            self::PREFERRED => 'Preferred',
            self::BONUS => 'Bonus',
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
            self::REQUIRED => '#dc3545', // Red
            self::PREFERRED => '#007bff', // Blue
            self::BONUS => '#28a745', // Green
        };
    }
}

