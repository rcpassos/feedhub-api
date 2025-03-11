<?php

namespace App\Enums;

trait ExtraMethods
{
    /**
     * Returns only the values of each option from the enum.
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Returns only the values of each option from the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
