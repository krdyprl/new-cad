<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * User Repository Interface
 * 
 * Defines contract for user data access operations
 */
interface UserRepositoryInterface
{
    /**
     * Get all users
     */
    public function all(): Collection;
    
    /**
     * Get paginated users
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Find user by ID
     */
    public function find(int $id): ?User;
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Create new user
     */
    public function create(array $data): User;
    
    /**
     * Update user
     */
    public function update(User $user, array $data): bool;
    
    /**
     * Delete user
     */
    public function delete(User $user): bool;
    
    /**
     * Get admin users
     */
    public function getAdmins(): Collection;
    
    /**
     * Get user count
     */
    public function count(): int;
}
