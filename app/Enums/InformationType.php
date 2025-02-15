<?php

namespace App\Enums;

/**
 * Information Type Enum
 * 
 * Represents all possible information types
 * Following type safety principles
 */
enum InformationType: string
{
    case NEWS = 'news';
    case INFORMATION = 'information';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::NEWS => 'Berita',
            self::INFORMATION => 'Informasi',
        };
    }

    /**
     * Get icon class for type display
     */
    public function iconClass(): string
    {
        return match ($this) {
            self::NEWS => 'fas fa-newspaper',
            self::INFORMATION => 'fas fa-info-circle',
        };
    }

    /**
     * Get all possible values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get options for form select
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
