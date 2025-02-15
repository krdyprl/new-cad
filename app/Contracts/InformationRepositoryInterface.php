<?php

namespace App\Contracts;

use App\Models\Information;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Information Repository Interface
 * 
 * Clean interface following SOLID principles
 */
interface InformationRepositoryInterface
{
    /**
     * Get all information
     */
    public function getAll(): Collection;

    /**
     * Get paginated information
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find information by ID
     */
    public function find(int $id): ?Information;

    /**
     * Find information by slug
     */
    public function findBySlug(string $slug): ?Information;

    /**
     * Get information by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get information by type
     */
    public function getByType(string $type): Collection;

    /**
     * Get recent information
     */
    public function getRecent(int $limit = 5): Collection;

    /**
     * Get featured information
     */
    public function getFeatured(): Collection;

    /**
     * Search information
     */
    public function search(string $query): Collection;

    /**
     * Create new information
     */
    public function create(array $data): Information;

    /**
     * Update information
     */
    public function update(Information $information, array $data): bool;

    /**
     * Delete information
     */
    public function delete(Information $information): bool;
}
