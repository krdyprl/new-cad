<?php

namespace App\Enums;

/**
 * User Role Enum
 * 
 * Represents all possible user roles
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'Pengguna',
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
     * Get all labels
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    /**
     * Check if role has admin privileges
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Get CSS class for role
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::ADMIN => 'badge-primary',
            self::USER => 'badge-secondary',
        };
    }
}
