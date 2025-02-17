<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

/**
 * Booking Policy
 * 
 * Handles authorization logic for booking operations following SOLID principles
 * Single Responsibility: Only handles booking authorization
 */
class BookingPolicyClean
{
    /**
     * Determine whether the user can view any bookings.
     */
    public function viewAny(User $user): bool
    {
        // Only admin can view all bookings
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        // User can view their own booking or admin can view any booking
        return $user->is_admin || $booking->user_id === $user->id;
    }

    /**
     * Determine whether the user can create bookings.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create bookings
        return true;
    }

    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        // User can update their own booking (within limits) or admin can update any
        if ($user->is_admin) {
            return true;
        }

        // Regular users can only update their own pending bookings
        return $booking->user_id === $user->id && $booking->status === 'pending';
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Only admin can delete bookings or user can cancel their own pending booking
        if ($user->is_admin) {
            return true;
        }

        // Regular users can only cancel their own pending bookings
        return $booking->user_id === $user->id && $booking->status === 'pending';
    }

    /**
     * Determine whether the user can manage booking status.
     */
    public function manageStatus(User $user, Booking $booking): bool
    {
        // Only admin can manage booking status
        return $user->is_admin;
    }

    /**
     * Determine whether the user can download booking PDF.
     */
    public function downloadPDF(User $user, Booking $booking): bool
    {
        // User can download their own booking PDF or admin can download any
        return $user->is_admin || $booking->user_id === $user->id;
    }
}
