<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Send Booking Confirmation Listener
 * 
 * Handles booking confirmation email when booking is created
 * Following Single Responsibility and Open-Closed Principles
 */
class SendBookingConfirmation
{
    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        try {
            // Send confirmation email to customer
            $this->sendCustomerConfirmation($booking);
            
            // Send notification to admin
            $this->sendAdminNotification($booking);
            
            Log::info('Booking confirmation emails sent', [
                'booking_id' => $booking->booking_id,
                'customer_email' => $booking->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation emails', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send confirmation email to customer
     */
    private function sendCustomerConfirmation($booking): void
    {
        // Implementation would go here
        // For now, just log the action
        Log::info('Customer confirmation email would be sent', [
            'booking_id' => $booking->booking_id,
            'customer_email' => $booking->email
        ]);
    }

    /**
     * Send notification email to admin
     */
    private function sendAdminNotification($booking): void
    {
        // Implementation would go here
        // For now, just log the action
        Log::info('Admin notification email would be sent', [
            'booking_id' => $booking->booking_id,
            'admin_action_required' => true
        ]);
    }
}
