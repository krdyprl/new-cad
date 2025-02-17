<?php

namespace App\Events;

use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Booking Status Updated Event
 * 
 * Dispatched when booking status is updated
 * Following Observer Pattern and Open-Closed Principle
 */
class BookingStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly BookingStatus $oldStatus,
        public readonly BookingStatus $newStatus
    ) {}
}
