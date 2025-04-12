<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Contracts\BookingRepositoryInterface;
use App\Events\BookingStatusUpdated;
use App\Enums\BookingStatus;
use App\Exceptions\BusinessLogicException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Booking Service
 * 
 * Handles booking-related business logic following SOLID principles:
 * - Single Responsibility: Only handles booking business logic
 * - Open/Closed: Can be extended without modification
 * - Liskov Substitution: Can work with different repository implementations
 * - Interface Segregation: Uses focused interfaces
 * - Dependency Inversion: Depends on repository abstraction
 */
class BookingService
{
    private const DEFAULT_PAGINATION_LIMIT = 10;
    private const RECENT_ITEMS_LIMIT = 5;

    public function __construct(
        private BookingRepositoryInterface $bookingRepository
    ) {}

    /**
     * Get paginated bookings with user relationship and enhanced logging
     */
    public function getPaginatedBookings(): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        Log::info('BookingService: Getting paginated bookings', [
            'limit' => self::DEFAULT_PAGINATION_LIMIT,
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);

        try {
            $result = $this->bookingRepository->getPaginated(self::DEFAULT_PAGINATION_LIMIT);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingService: Paginated bookings retrieved successfully', [
                'total_bookings' => $result->total(),
                'current_page' => $result->currentPage(),
                'per_page' => $result->perPage(),
                'last_page' => $result->lastPage(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingService: Failed to get paginated bookings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id()
            ]);

            throw new BusinessLogicException('Failed to retrieve bookings: ' . $e->getMessage());
        }
    }

    /**
     * Update booking status with business rules, event dispatching, and enhanced logging
     */
    public function updateStatus(Booking $booking, string $status, ?User $updatedBy = null): bool
    {
        $startTime = microtime(true);
        
        Log::info('BookingService: Status update initiated', [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_id,
            'current_status' => $booking->getStatus()->value,
            'new_status' => $status,
            'updated_by' => $updatedBy?->id,
            'timestamp' => now()
        ]);

        try {
            // Validate status exists in enum
            $bookingStatus = BookingStatus::tryFrom($status);
            if (!$bookingStatus) {
                Log::warning('BookingService: Invalid status provided', [
                    'booking_id' => $booking->id,
                    'invalid_status' => $status,
                    'valid_statuses' => BookingStatus::values()
                ]);
                throw new \InvalidArgumentException("Invalid booking status: {$status}");
            }

            $currentStatus = $booking->getStatus();

            // Check if status transition is allowed
            if (!$currentStatus->canUpdateTo($bookingStatus)) {
                Log::warning('BookingService: Invalid status transition attempted', [
                    'booking_id' => $booking->id,
                    'current_status' => $currentStatus->value,
                    'attempted_status' => $status,
                    'valid_next_statuses' => $currentStatus->getValidNextStatuses(),
                    'updated_by' => $updatedBy?->id
                ]);
                
                throw new BusinessLogicException(
                    "Cannot change booking status from {$currentStatus->value} to {$status}"
                );
            }

            // Begin database transaction
            return DB::transaction(function () use ($booking, $bookingStatus, $updatedBy, $currentStatus, $startTime) {
                Log::info('BookingService: Starting status update transaction', [
                    'booking_id' => $booking->id,
                    'transaction_started' => true
                ]);

                // Update booking status using the model method
                $updateResult = $booking->updateStatus($bookingStatus, $updatedBy);

                if (!$updateResult) {
                    Log::error('BookingService: Booking status update failed in model', [
                        'booking_id' => $booking->id,
                        'status' => $bookingStatus->value
                    ]);
                    throw new BusinessLogicException('Failed to update booking status');
                }

                // Dispatch status updated event
                try {
                    event(new BookingStatusUpdated($booking, $currentStatus, $bookingStatus, $updatedBy));
                    
                    Log::info('BookingService: Status updated event dispatched', [
                        'booking_id' => $booking->id,
                        'event' => 'BookingStatusUpdated',
                        'old_status' => $currentStatus->value,
                        'new_status' => $bookingStatus->value
                    ]);
                } catch (\Exception $eventException) {
                    Log::error('BookingService: Failed to dispatch status updated event', [
                        'booking_id' => $booking->id,
                        'event_error' => $eventException->getMessage(),
                        'trace' => $eventException->getTraceAsString()
                    ]);
                    // Don't fail the transaction for event dispatch failures
                }

                $endTime = microtime(true);
                $processingTime = round(($endTime - $startTime) * 1000, 2);

                Log::info('BookingService: Status update completed successfully', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_id,
                    'old_status' => $currentStatus->value,
                    'new_status' => $bookingStatus->value,
                    'updated_by' => $updatedBy?->id,
                    'processing_time_ms' => $processingTime,
                    'transaction_completed' => true
                ]);

                // Log activity on booking model
                if (method_exists($booking, 'logActivity')) {
                    $booking->logActivity('status_updated_via_service', [
                        'old_status' => $currentStatus->value,
                        'new_status' => $bookingStatus->value,
                        'updated_by' => $updatedBy?->id,
                        'processing_time_ms' => $processingTime,
                        'service_method' => 'BookingService::updateStatus'
                    ]);
                }

                return true;
            });

        } catch (BusinessLogicException $e) {
            // Re-throw business logic exceptions
            throw $e;
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingService: Status update failed with exception', [
                'booking_id' => $booking->id,
                'attempted_status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'updated_by' => $updatedBy?->id
            ]);

            throw new BusinessLogicException('Failed to update booking status: ' . $e->getMessage());
        }
    }

    /**
     * Delete a booking with business rules
     */
    public function delete(Booking $booking): bool
    {
        // Business rule: Can only delete pending or cancelled bookings
        if (!in_array($booking->status, ['pending', 'cancelled'])) {
            throw new \InvalidArgumentException("Cannot delete booking with status: {$booking->status}");
        }

        return $this->bookingRepository->delete($booking);
    }

    /**
     * Get bookings by status
     */
    public function getBookingsByStatus(string $status): Collection
    {
        return $this->bookingRepository->getByStatus($status);
    }

    /**
     * Get booking statistics
     */
    public function getBookingStats(): array
    {
        $stats = $this->bookingRepository->getStatistics();
        
        // Add calculated fields
        $stats['completion_rate'] = $stats['total'] > 0 
            ? round(($stats['completed'] / $stats['total']) * 100, 2) 
            : 0;

        return $stats;
    }

    /**
     * Create a new booking with business rules
     */
    public function create(array $data): Booking
    {
        // Generate unique booking ID if not provided
        if (!isset($data['booking_id'])) {
            $data['booking_id'] = $this->generateBookingId();
        }

        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = BookingStatus::PENDING->value;
        }

        return $this->bookingRepository->create($data);
    }

    /**
     * Update an existing booking
     */
    public function update(Booking $booking, array $data): bool
    {
        return $this->bookingRepository->update($booking, $data);
    }

    /**
     * Get recent bookings
     */
    public function getRecentBookings(): Collection
    {
        return $this->bookingRepository->getRecent(self::RECENT_ITEMS_LIMIT);
    }

    /**
     * Generate unique booking ID
     */
    private function generateBookingId(): string
    {
        do {
            $bookingId = 'BK' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Booking::where('booking_id', $bookingId)->exists());

        return $bookingId;
    }

    /**
     * Create booking for user with validation
     */
    public function createBooking(User $user, array $data): Booking
    {
        // Validate business rules
        $this->validateBookingData($data);
        
        // Generate unique booking ID
        $data['booking_id'] = $this->generateBookingId();
        $data['user_id'] = $user->id;
        $data['status'] = BookingStatus::PENDING->value;
        
        return $this->bookingRepository->create($data);
    }

    /**
     * Get user's bookings with pagination
     */
    public function getUserBookings(User $user): LengthAwarePaginator
    {
        return $user->bookings()
            ->orderBy('created_at', 'desc')
            ->paginate(self::DEFAULT_PAGINATION_LIMIT);
    }

    /**
     * Cancel booking with business rules
     */
    public function cancelBooking(Booking $booking): bool
    {
        // Business rule: Can only cancel pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            throw new BusinessLogicException(
                "Cannot cancel booking with status: {$booking->status}"
            );
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => BookingStatus::CANCELLED->value]);

        // Dispatch event for status change
        BookingStatusUpdated::dispatch($booking, $oldStatus, BookingStatus::CANCELLED->value);

        return true;
    }

    /**
     * Validate booking data
     */
    private function validateBookingData(array $data): void
    {
        // Check if date is not in the past
        if (isset($data['date']) && strtotime($data['date']) < strtotime('today')) {
            throw new BusinessLogicException('Tanggal booking tidak boleh di masa lalu.');
        }

        // Check if time slot is available (if needed)
        if (isset($data['date']) && isset($data['time'])) {
            $existingBooking = Booking::where('date', $data['date'])
                ->where('time', $data['time'])
                ->where('status', '!=', BookingStatus::CANCELLED->value)
                ->exists();
                
            if ($existingBooking) {
                throw new BusinessLogicException('Slot waktu tersebut sudah terisi.');
            }
        }
    }
}
