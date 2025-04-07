<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Booking Repository
 * 
 * Clean implementation following SOLID principles
 * Implements Repository pattern for data access abstraction
 */
class BookingRepository implements BookingRepositoryInterface
{
    public function __construct(private Booking $model)
    {
        Log::debug('BookingRepository initialized', [
            'model_class' => get_class($this->model),
            'timestamp' => now()
        ]);
    }

    /**
     * Get all bookings with enhanced logging
     */
    public function getAll(): Collection
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting all bookings', [
            'method' => 'getAll',
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);

        try {
            $bookings = $this->model->all();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: All bookings retrieved successfully', [
                'total_bookings' => $bookings->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $bookings;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get all bookings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get paginated bookings with enhanced logging and caching
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting paginated bookings', [
            'per_page' => $perPage,
            'method' => 'getPaginated',
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);

        try {
            // Cache key includes page info and user context for security
            $page = request()->input('page', 1);
            $cacheKey = "bookings_paginated_{$perPage}_{$page}_" . auth()->id();
            
            $result = Cache::remember($cacheKey, 300, function () use ($perPage) { // 5-minute cache
                Log::info('BookingRepository: Building paginated bookings cache', [
                    'per_page' => $perPage
                ]);

                return $this->model
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Paginated bookings retrieved successfully', [
                'total_bookings' => $result->total(),
                'current_page' => $result->currentPage(),
                'per_page' => $result->perPage(),
                'last_page' => $result->lastPage(),
                'processing_time_ms' => $processingTime,
                'data_source' => 'cache',
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get paginated bookings', [
                'per_page' => $perPage,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Find booking by ID with enhanced logging
     */
    public function find(int $id): ?Booking
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Finding booking by ID', [
            'booking_id' => $id,
            'method' => 'find',
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);

        try {
            $booking = $this->model->find($id);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            if ($booking) {
                Log::info('BookingRepository: Booking found successfully', [
                    'booking_id' => $id,
                    'booking_code' => $booking->booking_id,
                    'status' => $booking->status?->value ?? $booking->status,
                    'user_id' => $booking->user_id,
                    'processing_time_ms' => $processingTime
                ]);
            } else {
                Log::warning('BookingRepository: Booking not found', [
                    'booking_id' => $id,
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $booking;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Error finding booking by ID', [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Find booking by ID with relationships
     */
    public function findWithRelations(int $id, array $relations = ['user']): ?Booking
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * Get bookings by status with enhanced logging and enum support
     */
    public function getByStatus(string $status): Collection
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting bookings by status', [
            'status' => $status,
            'method' => 'getByStatus',
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);

        try {
            // Validate status if it's a valid enum value
            $validStatus = BookingStatus::tryFrom($status);
            if (!$validStatus) {
                Log::warning('BookingRepository: Invalid status provided', [
                    'invalid_status' => $status,
                    'valid_statuses' => BookingStatus::values()
                ]);
            }

            $bookings = $this->model->where('status', $status)->get();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Bookings by status retrieved successfully', [
                'status' => $status,
                'total_bookings' => $bookings->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $bookings;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get bookings by status', [
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get recent bookings
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get bookings by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    /**
     * Get booking statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->where('status', 'pending')->count(),
            'confirmed' => $this->model->where('status', 'confirmed')->count(),
            'completed' => $this->model->where('status', 'completed')->count(),
            'cancelled' => $this->model->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Create new booking with enhanced logging and validation
     */
    public function create(array $data): Booking
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Creating new booking', [
            'method' => 'create',
            'data_keys' => array_keys($data),
            'package' => $data['package'] ?? 'unknown',
            'participants' => $data['participants'] ?? 'unknown',
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Start database transaction
            $booking = DB::transaction(function () use ($data) {
                Log::info('BookingRepository: Starting booking creation transaction');
                
                $booking = $this->model->create($data);
                
                Log::info('BookingRepository: Booking created in database', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_id,
                    'user_id' => $booking->user_id,
                    'status' => $booking->status?->value ?? $booking->status
                ]);

                return $booking;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Booking created successfully', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'package' => $booking->package,
                'total_amount' => $booking->total,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Log activity on the booking
            if (method_exists($booking, 'logActivity')) {
                $booking->logActivity('booking_created_via_repository', [
                    'repository_method' => 'BookingRepository::create',
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $booking;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to create booking', [
                'data_keys' => array_keys($data),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Update booking with enhanced logging and validation
     */
    public function update(Booking $booking, array $data): bool
    {
        $startTime = microtime(true);
        $originalData = $booking->toArray();
        
        Log::info('BookingRepository: Updating booking', [
            'method' => 'update',
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_id,
            'current_status' => $booking->status?->value ?? $booking->status,
            'update_data_keys' => array_keys($data),
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Start database transaction
            $result = DB::transaction(function () use ($booking, $data) {
                Log::info('BookingRepository: Starting booking update transaction', [
                    'booking_id' => $booking->id
                ]);
                
                $result = $booking->update($data);
                
                Log::info('BookingRepository: Booking updated in database', [
                    'booking_id' => $booking->id,
                    'new_status' => $booking->fresh()?->status?->value ?? $booking->fresh()?->status,
                    'updated_fields' => array_keys($data),
                    'update_result' => $result
                ]);

                return $result;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            // Log what changed
            $changes = [];
            foreach ($data as $key => $value) {
                if (isset($originalData[$key]) && $originalData[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value
                    ];
                }
            }
            
            Log::info('BookingRepository: Booking updated successfully', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'changes' => $changes,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Log activity on the booking
            if (method_exists($booking, 'logActivity')) {
                $booking->logActivity('booking_updated_via_repository', [
                    'repository_method' => 'BookingRepository::update',
                    'changes' => $changes,
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to update booking', [
                'booking_id' => $booking->id,
                'update_data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Update booking status with enhanced logging
     */
    public function updateStatus(Booking $booking, string $status): bool
    {
        $startTime = microtime(true);
        $originalStatus = $booking->status?->value ?? $booking->status;
        
        Log::info('BookingRepository: Updating booking status', [
            'method' => 'updateStatus',
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_id,
            'old_status' => $originalStatus,
            'new_status' => $status,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Validate status with enum if available
            if (enum_exists('App\Enums\BookingStatus')) {
                $validStatuses = array_column(\App\Enums\BookingStatus::cases(), 'value');
                if (!in_array($status, $validStatuses)) {
                    Log::warning('BookingRepository: Invalid booking status provided', [
                        'provided_status' => $status,
                        'valid_statuses' => $validStatuses,
                        'booking_id' => $booking->id
                    ]);
                }
            }

            $result = $booking->update(['status' => $status]);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Booking status updated successfully', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'status_change' => "{$originalStatus} -> {$status}",
                'update_result' => $result,
                'processing_time_ms' => $processingTime
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Log activity on the booking
            if (method_exists($booking, 'logActivity')) {
                $booking->logActivity('booking_status_updated_via_repository', [
                    'repository_method' => 'BookingRepository::updateStatus',
                    'old_status' => $originalStatus,
                    'new_status' => $status,
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to update booking status', [
                'booking_id' => $booking->id,
                'old_status' => $originalStatus,
                'new_status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Delete booking with enhanced logging and cleanup
     */
    public function delete(Booking $booking): bool
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Deleting booking', [
            'method' => 'delete',
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_id,
            'status' => $booking->status?->value ?? $booking->status,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Store booking data for logging
            $bookingData = [
                'id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'package' => $booking->package,
                'user_id' => $booking->user_id,
                'status' => $booking->status?->value ?? $booking->status,
                'total' => $booking->total
            ];

            // Start database transaction
            $result = DB::transaction(function () use ($booking) {
                Log::info('BookingRepository: Starting booking deletion transaction', [
                    'booking_id' => $booking->id
                ]);
                
                // Log activity before deletion
                if (method_exists($booking, 'logActivity')) {
                    $booking->logActivity('booking_deleted_via_repository', [
                        'repository_method' => 'BookingRepository::delete',
                        'deleted_at' => now()
                    ]);
                }
                
                $result = $booking->delete();
                
                Log::info('BookingRepository: Booking deleted from database', [
                    'booking_id' => $booking->id,
                    'deletion_result' => $result
                ]);

                return $result;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Booking deleted successfully', [
                'deleted_booking' => $bookingData,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to delete booking', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get bookings by month and year with enhanced logging
     */
    public function getByMonth(int $month, int $year): Collection
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting bookings by month and year', [
            'method' => 'getByMonth',
            'month' => $month,
            'year' => $year,
            'timestamp' => now()
        ]);

        try {
            $bookings = $this->model
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->get();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Bookings retrieved by month and year', [
                'month' => $month,
                'year' => $year,
                'total_bookings' => $bookings->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $bookings;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get bookings by month and year', [
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get completed bookings by date range with enhanced logging
     */
    public function getCompletedByDateRange(string $startDate, string $endDate): Collection
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting completed bookings by date range', [
            'method' => 'getCompletedByDateRange',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'timestamp' => now()
        ]);

        try {
            $bookings = $this->model
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Completed bookings retrieved by date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_completed_bookings' => $bookings->count(),
                'total_amount' => $bookings->sum('total'),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $bookings;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get completed bookings by date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get package statistics with enhanced logging and caching
     */
    public function getPackageStatistics(): array
    {
        $startTime = microtime(true);
        
        Log::info('BookingRepository: Getting package statistics', [
            'method' => 'getPackageStatistics',
            'timestamp' => now()
        ]);

        try {
            // Try to get from cache first
            $cacheKey = 'package_statistics';
            $statistics = Cache::remember($cacheKey, 600, function () {
                Log::info('BookingRepository: Generating package statistics (cache miss)');
                
                return $this->model
                    ->selectRaw('package, COUNT(*) as count')
                    ->whereNotNull('package')
                    ->groupBy('package')
                    ->pluck('count', 'package')
                    ->toArray();
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('BookingRepository: Package statistics retrieved', [
                'total_packages' => count($statistics),
                'statistics' => $statistics,
                'processing_time_ms' => $processingTime,
                'cache_key' => $cacheKey,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $statistics;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('BookingRepository: Failed to get package statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Clear relevant caches when booking data changes
     */
    private function clearRelevantCaches(): void
    {
        try {
            Log::info('BookingRepository: Clearing relevant caches');
            
            $cacheKeys = [
                'bookings_all',
                'bookings_paginated',
                'booking_statistics',
                'recent_bookings'
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
                Log::debug("BookingRepository: Cleared cache key: {$key}");
            }
            
            // Clear user-specific caches if user is authenticated
            if (auth()->check()) {
                $userId = auth()->id();
                $userCacheKeys = [
                    "user_{$userId}_bookings",
                    "user_{$userId}_recent_bookings"
                ];
                
                foreach ($userCacheKeys as $key) {
                    Cache::forget($key);
                    Log::debug("BookingRepository: Cleared user cache key: {$key}");
                }
            }
            
            Log::info('BookingRepository: All relevant caches cleared successfully');
            
        } catch (\Exception $e) {
            Log::error('BookingRepository: Failed to clear caches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
