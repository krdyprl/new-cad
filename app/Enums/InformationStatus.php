<?php

namespace App\Enums;

/**
 * Information Status Enum
 * 
 * Represents all possible information statuses
 * Following type safety principles
 */
enum InformationStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Dipublikasikan',
        };
    }

    /**
     * Get badge CSS class for status display
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'badge-secondary',
            self::PUBLISHED => 'badge-success',
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

    /**
     * Check if information is visible to public
     */
    public function isPublic(): bool
    {
        return $this === self::PUBLISHED;
    }
}
