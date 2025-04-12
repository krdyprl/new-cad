<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use App\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * User Service
 * 
 * Handles user-related business logic following SOLID principles:
 * - Single Responsibility: Only handles user business logic
 * - Open/Closed: Can be extended without modification  
 * - Liskov Substitution: Can work with different repository implementations
 * - Interface Segregation: Uses focused interfaces
 * - Dependency Inversion: Depends on repository abstraction
 */
class UserService
{
    private const DEFAULT_PAGINATION_LIMIT = 10;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get paginated users
     */
    public function getPaginatedUsers(): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated(self::DEFAULT_PAGINATION_LIMIT);
    }

    /**
     * Delete user and related data with business rules
     */
    public function delete(User $user): bool
    {
        // Business rule: Cannot delete admin users if they're the last admin
        if ($user->role === UserRole::ADMIN->value) {
            $adminCount = $this->userRepository->getAdmins()->count();
            if ($adminCount <= 1) {
                throw new \InvalidArgumentException("Cannot delete the last admin user");
            }
        }

        return $this->userRepository->delete($user);
    }

    /**
     * Create a new user with validation
     */
    public function create(array $data): User
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = UserRole::USER->value;
        }

        // Validate role
        if (!UserRole::tryFrom($data['role'])) {
            throw new \InvalidArgumentException("Invalid user role: {$data['role']}");
        }

        return $this->userRepository->create($data);
    }

    /**
     * Update an existing user
     */
    public function update(User $user, array $data): bool
    {
        // Hash password if being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Validate role if being updated
        if (isset($data['role']) && !UserRole::tryFrom($data['role'])) {
            throw new \InvalidArgumentException("Invalid user role: {$data['role']}");
        }

        return $this->userRepository->update($user, $data);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->userRepository->getByRole($role);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        $stats = $this->userRepository->getStatistics();
        
        // Add calculated fields
        $stats['admin_percentage'] = $stats['total'] > 0 
            ? round(($stats['admins'] / $stats['total']) * 100, 2) 
            : 0;

        $stats['verification_rate'] = $stats['total'] > 0 
            ? round(($stats['verified'] / $stats['total']) * 100, 2) 
            : 0;

        return $stats;
    }

    /**
     * Toggle user role between admin and user
     */
    public function toggleRole(User $user): bool
    {
        $currentRole = UserRole::from($user->role);
        $newRole = $currentRole === UserRole::ADMIN ? UserRole::USER : UserRole::ADMIN;
        
        // Check business rule for admin deletion
        if ($currentRole === UserRole::ADMIN) {
            $adminCount = $this->userRepository->getAdmins()->count();
            if ($adminCount <= 1) {
                throw new \InvalidArgumentException("Cannot change role of the last admin user");
            }
        }
        
        return $this->userRepository->update($user, ['role' => $newRole->value]);
    }

    /**
     * Verify user email
     */
    public function verifyEmail(User $user): bool
    {
        return $this->userRepository->update($user, ['email_verified_at' => now()]);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5): Collection
    {
        return $this->userRepository->getRecent($limit);
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->update($user, [
            'password' => Hash::make($newPassword)
        ]);
    }
}
