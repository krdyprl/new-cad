<?php

namespace App\Repositories;

use App\Models\Information;
use App\Contracts\InformationRepositoryInterface;
use App\Constants\AppConstants;
use App\Enums\InformationType;
use App\Enums\InformationStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Information Repository
 * 
 * Handles data access logic for information/content with comprehensive CMS logging,
 * content management audit trail, caching, and performance optimization
 * Implements InformationRepositoryInterface following Dependency Inversion Principle
 */
class InformationRepository implements InformationRepositoryInterface
{
    public function __construct(private Information $model)
    {
        Log::info('InformationRepository: Repository initialized', [
            'class' => self::class,
            'model' => get_class($this->model),
            'timestamp' => now()
        ]);
    }

    /**
     * Get all information entries with enhanced logging and caching
     */
    public function getAll(): Collection
    {
        $startTime = microtime(true);
        
        Log::info('InformationRepository: Getting all information entries', [
            'method' => 'getAll',
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Use caching for frequently accessed content
            $cacheKey = 'information_all';
            $information = Cache::remember($cacheKey, 300, function () {
                Log::info('InformationRepository: Generating information list (cache miss)');
                return $this->model->all();
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('InformationRepository: All information retrieved successfully', [
                'total_entries' => $information->count(),
                'published_count' => $information->where('status', 'published')->count(),
                'draft_count' => $information->where('status', 'draft')->count(),
                'news_count' => $information->where('type', 'news')->count(),
                'information_count' => $information->where('type', 'information')->count(),
                'processing_time_ms' => $processingTime,
                'cache_key' => $cacheKey,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $information;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('InformationRepository: Failed to get all information', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    public function all(): Collection
    {
        Log::info('InformationRepository: Legacy method called', [
            'method' => 'all',
            'note' => 'Consider using getAll() instead'
        ]);
        
        return $this->getAll();
    }

    /**
     * Get paginated information entries
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find information by ID
     */
    public function find(int $id): ?Information
    {
        return $this->model->find($id);
    }

    /**
     * Find information by slug
     */
    public function findBySlug(string $slug): ?Information
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get information by type
     */
    public function getByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Get information by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get published information
     */
    public function getPublished(): Collection
    {
        return $this->model
            ->where('status', AppConstants::INFO_STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get recent information
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get featured information
     */
    public function getFeatured(): Collection
    {
        return $this->model
            ->where('status', AppConstants::INFO_STATUS_PUBLISHED)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create new information
     */
    public function create(array $data): Information
    {
        // Set published_at if status is published
        if ($data['status'] === AppConstants::INFO_STATUS_PUBLISHED && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Set author_id if not provided
        if (!isset($data['author_id'])) {
            $data['author_id'] = auth()->id();
        }

        return $this->model->create($data);
    }

    /**
     * Update information
     */
    public function update(Information $information, array $data): bool
    {
        // Set published_at if status is being changed to published
        if ($data['status'] === AppConstants::INFO_STATUS_PUBLISHED && !$information->published_at) {
            $data['published_at'] = now();
        }

        return $information->update($data);
    }

    /**
     * Delete information
     */
    public function delete(Information $information): bool
    {
        return $information->delete();
    }

    /**
     * Get information statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'published' => $this->model->where('status', AppConstants::INFO_STATUS_PUBLISHED)->count(),
            'draft' => $this->model->where('status', AppConstants::INFO_STATUS_DRAFT)->count(),
            'news' => $this->model->where('type', AppConstants::INFO_TYPE_NEWS)->count(),
            'information' => $this->model->where('type', AppConstants::INFO_TYPE_INFORMATION)->count(),
        ];
    }

    /**
     * Search information by keyword
     */
    public function search(string $keyword): Collection
    {
        return $this->model
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->get();
    }
}
