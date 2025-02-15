<?php

namespace App\Contracts;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Booking Repository Interface
 * 
 * Defines contract for booking data access operations
 * Following Interface Segregation Principle
 */
interface BookingRepositoryInterface
{
    /**
     * Find booking by ID
     */
    public function find(int $id): ?Booking;

    /**
     * Get all bookings
     */
    public function getAll(): Collection;

    /**
     * Get paginated bookings
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Create new booking
     */
    public function create(array $data): Booking;

    /**
     * Update booking
     */
    public function update(Booking $booking, array $data): bool;

    /**
     * Update booking status
     */
    public function updateStatus(Booking $booking, string $status): bool;

    /**
     * Delete booking
     */
    public function delete(Booking $booking): bool;

    /**
     * Get bookings by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get recent bookings
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Get booking statistics
     */
    public function getStatistics(): array;

    /**
     * Get bookings by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Get bookings by month and year
     */
    public function getByMonth(int $month, int $year): Collection;

    /**
     * Get completed bookings by date range
     */
    public function getCompletedByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Get package statistics
     */
    public function getPackageStatistics(): array;
}
