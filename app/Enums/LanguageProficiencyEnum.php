<?php

namespace App\Enums;

enum LanguageProficiencyEnum: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case NATIVE = 'native';

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
            self::BEGINNER => 'Basic',
            self::INTERMEDIATE => 'Conversational',
            self::ADVANCED => 'Fluent',
            self::NATIVE => 'Native',
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
            self::BEGINNER => '#6c757d', // Gray
            self::INTERMEDIATE => '#17a2b8', // Cyan
            self::ADVANCED => '#007bff', // Blue
            self::NATIVE => '#28a745', // Green
        };
    }
}

