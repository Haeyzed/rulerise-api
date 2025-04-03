<?php

namespace App\Enums;

enum SalaryPeriodEnum: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

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
            self::HOURLY => 'Per Hour',
            self::DAILY => 'Per Day',
            self::WEEKLY => 'Per Week',
            self::MONTHLY => 'Per Month',
            self::YEARLY => 'Per Year',
        };
    }
}

