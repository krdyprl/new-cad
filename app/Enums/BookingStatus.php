<?php

namespace App\Enums;

/**
 * Booking Status Enum
 * 
 * Represents all possible booking statuses
 */
enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Konfirmasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
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
     * Get CSS class for status
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::CONFIRMED => 'badge-info',
            self::COMPLETED => 'badge-success',
            self::CANCELLED => 'badge-danger',
        };
    }

    /**
     * Check if status can be updated to another status
     * Following business rules for status transitions
     */
    public function canUpdateTo(BookingStatus $newStatus): bool
    {
        return match ($this) {
            self::PENDING => in_array($newStatus, [self::CONFIRMED, self::CANCELLED]),
            self::CONFIRMED => in_array($newStatus, [self::COMPLETED, self::CANCELLED]),
            self::COMPLETED => false, // Completed bookings cannot be changed
            self::CANCELLED => false, // Cancelled bookings cannot be changed
        };
    }

    /**
     * Get valid next statuses for current status
     */
    public function getValidNextStatuses(): array
    {
        return match ($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED => [],
            self::CANCELLED => [],
        };
    }

    /**
     * Check if status is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED]);
    }

    /**
     * Check if status is active (not cancelled)
     */
    public function isActive(): bool
    {
        return $this !== self::CANCELLED;
    }
}
