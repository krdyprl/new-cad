<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use App\Constants\AppConstants;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * User Repository
 * 
 * Handles data access logic for users with comprehensive logging,
 * authentication tracking, role management, and security features
 * Implements UserRepositoryInterface following Dependency Inversion Principle
 */
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private User $model)
    {
        Log::info('UserRepository: Repository initialized', [
            'class' => self::class,
            'model' => get_class($this->model),
            'timestamp' => now()
        ]);
    }

    /**
     * Get all users with enhanced logging and caching
     */
    public function getAll(): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting all users', [
            'method' => 'getAll',
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Use caching for frequently accessed data
            $cacheKey = 'users_all';
            $users = Cache::remember($cacheKey, 300, function () {
                Log::info('UserRepository: Generating users list (cache miss)');
                return $this->model->all();
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: All users retrieved successfully', [
                'total_users' => $users->count(),
                'admin_count' => $users->where('role', 'admin')->count(),
                'user_count' => $users->where('role', 'user')->count(),
                'processing_time_ms' => $processingTime,
                'cache_key' => $cacheKey,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get all users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get paginated users with enhanced logging and performance tracking
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting paginated users', [
            'method' => 'getPaginated',
            'per_page' => $perPage,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $users = $this->model
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Paginated users retrieved successfully', [
                'total_users' => $users->total(),
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
                'users_on_page' => $users->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get paginated users', [
                'per_page' => $perPage,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Find user by ID with comprehensive logging and security tracking
     */
    public function find(int $id): ?User
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Finding user by ID', [
            'method' => 'find',
            'user_id' => $id,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $user = $this->model->find($id);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            if ($user) {
                Log::info('UserRepository: User found successfully', [
                    'found_user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $user->role?->value ?? $user->role,
                    'email_verified' => $user->email_verified_at ? 'yes' : 'no',
                    'processing_time_ms' => $processingTime
                ]);

                // Log user access for security audit
                if (method_exists($user, 'logActivity')) {
                    $user->logActivity('user_accessed_via_repository', [
                        'repository_method' => 'UserRepository::find',
                        'accessed_by' => auth()->id(),
                        'processing_time_ms' => $processingTime
                    ]);
                }
            } else {
                Log::warning('UserRepository: User not found', [
                    'requested_user_id' => $id,
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $user;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to find user by ID', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Find user by email with authentication security logging
     */
    public function findByEmail(string $email): ?User
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Finding user by email', [
            'method' => 'findByEmail',
            'email' => $email,
            'requested_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        try {
            $user = $this->model->where('email', $email)->first();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            if ($user) {
                Log::info('UserRepository: User found by email', [
                    'found_user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role?->value ?? $user->role,
                    'email_verified' => $user->email_verified_at ? 'yes' : 'no',
                    'last_login' => $user->updated_at ?? 'never',
                    'processing_time_ms' => $processingTime
                ]);

                // Security audit log for email-based user lookup
                if (method_exists($user, 'logActivity')) {
                    $user->logActivity('user_found_by_email_via_repository', [
                        'repository_method' => 'UserRepository::findByEmail',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'processing_time_ms' => $processingTime
                    ]);
                }
            } else {
                // Security log for failed email lookup (potential attack)
                Log::warning('UserRepository: User not found by email (potential security event)', [
                    'email' => $email,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $user;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to find user by email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get users by role with enum support and comprehensive logging
     */
    public function getByRole(string $role): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting users by role', [
            'method' => 'getByRole',
            'role' => $role,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Validate role with enum if available
            if (enum_exists('App\Enums\UserRole')) {
                $validRoles = array_column(\App\Enums\UserRole::cases(), 'value');
                if (!in_array($role, $validRoles)) {
                    Log::warning('UserRepository: Invalid role provided', [
                        'provided_role' => $role,
                        'valid_roles' => $validRoles
                    ]);
                }
            }

            $users = $this->model->where('role', $role)->get();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Users retrieved by role', [
                'role' => $role,
                'total_users' => $users->count(),
                'verified_users' => $users->whereNotNull('email_verified_at')->count(),
                'unverified_users' => $users->whereNull('email_verified_at')->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get users by role', [
                'role' => $role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get admin users with enhanced security logging
     */
    public function getAdmins(): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting admin users', [
            'method' => 'getAdmins',
            'requested_by' => auth()->id(),
            'security_event' => 'admin_list_access',
            'timestamp' => now()
        ]);

        try {
            $admins = $this->getByRole('admin');
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            // Security audit log for admin access
            Log::info('UserRepository: Admin users list accessed', [
                'total_admins' => $admins->count(),
                'admin_names' => $admins->pluck('name')->toArray(),
                'requested_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'processing_time_ms' => $processingTime
            ]);

            return $admins;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get admin users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get regular users (non-admin) with performance tracking
     */
    public function getRegularUsers(): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting regular users', [
            'method' => 'getRegularUsers',
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $users = $this->getByRole('user');
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Regular users retrieved', [
                'total_regular_users' => $users->count(),
                'processing_time_ms' => $processingTime
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get regular users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get recent users with enhanced logging and caching
     */
    public function getRecent(int $limit = 5): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting recent users', [
            'method' => 'getRecent',
            'limit' => $limit,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Use caching for recent users
            $cacheKey = "recent_users_{$limit}";
            $users = Cache::remember($cacheKey, 300, function () use ($limit) {
                Log::info('UserRepository: Generating recent users list (cache miss)', [
                    'limit' => $limit
                ]);
                
                return $this->model
                    ->orderBy('created_at', 'desc')
                    ->take($limit)
                    ->get();
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Recent users retrieved successfully', [
                'total_recent_users' => $users->count(),
                'limit' => $limit,
                'user_names' => $users->pluck('name')->toArray(),
                'processing_time_ms' => $processingTime,
                'cache_key' => $cacheKey,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get recent users', [
                'limit' => $limit,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Create new user with comprehensive security and validation logging
     */
    public function create(array $data): User
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Creating new user', [
            'method' => 'create',
            'data_keys' => array_keys($data),
            'email' => $data['email'] ?? 'unknown',
            'role' => $data['role'] ?? 'unknown',
            'created_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        try {
            // Start database transaction
            $user = DB::transaction(function () use ($data) {
                Log::info('UserRepository: Starting user creation transaction');
                
                // Hash password securely
                if (isset($data['password'])) {
                    $originalPasswordLength = strlen($data['password']);
                    $data['password'] = Hash::make($data['password']);
                    
                    Log::info('UserRepository: Password hashed successfully', [
                        'original_password_length' => $originalPasswordLength,
                        'hash_algorithm' => 'bcrypt'
                    ]);
                }

                $user = $this->model->create($data);
                
                Log::info('UserRepository: User created in database', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_role' => $user->role?->value ?? $user->role
                ]);

                return $user;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: User created successfully', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role?->value ?? $user->role,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Log activity on the user
            if (method_exists($user, 'logActivity')) {
                $user->logActivity('user_created_via_repository', [
                    'repository_method' => 'UserRepository::create',
                    'created_by' => auth()->id(),
                    'processing_time_ms' => $processingTime
                ]);
            }

            // Security audit log for user creation
            Log::info('UserRepository: Security audit - New user created', [
                'event_type' => 'user_registration',
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role?->value ?? $user->role,
                'created_by' => auth()->id(),
                'ip_address' => request()->ip()
            ]);

            return $user;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to create user', [
                'data_keys' => array_keys($data),
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Update user with comprehensive change tracking and security logging
     */
    public function update(User $user, array $data): bool
    {
        $startTime = microtime(true);
        $originalData = $user->toArray();
        
        Log::info('UserRepository: Updating user', [
            'method' => 'update',
            'user_id' => $user->id,
            'user_email' => $user->email,
            'current_role' => $user->role?->value ?? $user->role,
            'update_data_keys' => array_keys($data),
            'updated_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        try {
            // Start database transaction
            $result = DB::transaction(function () use ($user, $data) {
                Log::info('UserRepository: Starting user update transaction', [
                    'user_id' => $user->id
                ]);
                
                // Handle password update securely
                if (isset($data['password']) && !empty($data['password'])) {
                    $originalPasswordLength = strlen($data['password']);
                    $data['password'] = Hash::make($data['password']);
                    
                    Log::info('UserRepository: Password updated for user', [
                        'user_id' => $user->id,
                        'original_password_length' => $originalPasswordLength,
                        'hash_algorithm' => 'bcrypt'
                    ]);
                } else {
                    unset($data['password']);
                }
                
                $result = $user->update($data);
                
                Log::info('UserRepository: User updated in database', [
                    'user_id' => $user->id,
                    'update_result' => $result,
                    'updated_fields' => array_keys($data)
                ]);

                return $result;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            // Log what changed
            $changes = [];
            foreach ($data as $key => $value) {
                if ($key !== 'password' && isset($originalData[$key]) && $originalData[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value
                    ];
                }
            }
            
            if (isset($data['password'])) {
                $changes['password'] = '[CHANGED]';
            }
            
            Log::info('UserRepository: User updated successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'changes' => $changes,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Log activity on the user
            if (method_exists($user, 'logActivity')) {
                $user->logActivity('user_updated_via_repository', [
                    'repository_method' => 'UserRepository::update',
                    'changes' => $changes,
                    'updated_by' => auth()->id(),
                    'processing_time_ms' => $processingTime
                ]);
            }

            // Security audit log for sensitive changes
            if (isset($changes['role']) || isset($changes['email']) || isset($changes['password'])) {
                Log::info('UserRepository: Security audit - Sensitive user data updated', [
                    'event_type' => 'user_sensitive_update',
                    'user_id' => $user->id,
                    'updated_by' => auth()->id(),
                    'sensitive_changes' => array_intersect_key($changes, array_flip(['role', 'email', 'password'])),
                    'ip_address' => request()->ip()
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to update user', [
                'user_id' => $user->id,
                'update_data' => array_keys($data),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Delete user with comprehensive security logging and cleanup
     */
    public function delete(User $user): bool
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Deleting user', [
            'method' => 'delete',
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role?->value ?? $user->role,
            'deleted_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        try {
            // Store user data for logging
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->value ?? $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at
            ];

            // Start database transaction
            $result = DB::transaction(function () use ($user) {
                Log::info('UserRepository: Starting user deletion transaction', [
                    'user_id' => $user->id
                ]);
                
                // Log activity before deletion
                if (method_exists($user, 'logActivity')) {
                    $user->logActivity('user_deleted_via_repository', [
                        'repository_method' => 'UserRepository::delete',
                        'deleted_by' => auth()->id(),
                        'deleted_at' => now()
                    ]);
                }
                
                $result = $user->delete();
                
                Log::info('UserRepository: User deleted from database', [
                    'user_id' => $user->id,
                    'deletion_result' => $result
                ]);

                return $result;
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: User deleted successfully', [
                'deleted_user' => $userData,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            // Security audit log for user deletion
            Log::info('UserRepository: Security audit - User deleted', [
                'event_type' => 'user_deletion',
                'deleted_user' => $userData,
                'deleted_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'processing_time_ms' => $processingTime
            ]);

            return $result;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to delete user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get user statistics with enhanced logging and caching
     */
    public function getStatistics(): array
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting user statistics', [
            'method' => 'getStatistics',
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            // Use caching for statistics
            $cacheKey = 'user_statistics';
            $statistics = Cache::remember($cacheKey, 600, function () {
                Log::info('UserRepository: Generating user statistics (cache miss)');
                
                return [
                    'total' => $this->model->count(),
                    'admins' => $this->model->where('role', AppConstants::ROLE_ADMIN)->count(),
                    'users' => $this->model->where('role', AppConstants::ROLE_USER)->count(),
                    'verified' => $this->getVerifiedUsersCount(),
                    'unverified' => $this->getUnverifiedUsersCount(),
                ];
            });
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: User statistics retrieved', [
                'statistics' => $statistics,
                'processing_time_ms' => $processingTime,
                'cache_key' => $cacheKey,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $statistics;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get user statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get verified users count with logging
     */
    public function getVerifiedUsersCount(): int
    {
        $startTime = microtime(true);
        
        try {
            $count = $this->model->whereNotNull('email_verified_at')->count();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::debug('UserRepository: Verified users count retrieved', [
                'verified_count' => $count,
                'processing_time_ms' => $processingTime
            ]);

            return $count;

        } catch (\Exception $e) {
            Log::error('UserRepository: Failed to get verified users count', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get unverified users count with logging
     */
    public function getUnverifiedUsersCount(): int
    {
        $startTime = microtime(true);
        
        try {
            $count = $this->model->whereNull('email_verified_at')->count();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::debug('UserRepository: Unverified users count retrieved', [
                'unverified_count' => $count,
                'processing_time_ms' => $processingTime
            ]);

            return $count;

        } catch (\Exception $e) {
            Log::error('UserRepository: Failed to get unverified users count', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get users by date range with enhanced logging
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting users by date range', [
            'method' => 'getByDateRange',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $users = $this->model
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Users retrieved by date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_users' => $users->count(),
                'admin_count' => $users->where('role', 'admin')->count(),
                'user_count' => $users->where('role', 'user')->count(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $users;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('UserRepository: Failed to get users by date range', [
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
     * Legacy method for backward compatibility
     */
    public function all(): Collection
    {
        Log::info('UserRepository: Legacy method called', [
            'method' => 'all',
            'note' => 'Consider using getAll() instead'
        ]);
        
        return $this->getAll();
    }

    /**
     * Clear relevant caches when user data changes
     */
    private function clearRelevantCaches(): void
    {
        try {
            Log::info('UserRepository: Clearing relevant caches');
            
            $cacheKeys = [
                'users_all',
                'user_statistics',
                'recent_users_5',
                'recent_users_10',
                'recent_users_15'
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
                Log::debug("UserRepository: Cleared cache key: {$key}");
            }
            
            // Clear role-specific caches
            $roleCacheKeys = [
                'users_by_role_admin',
                'users_by_role_user'
            ];
            
            foreach ($roleCacheKeys as $key) {
                Cache::forget($key);
                Log::debug("UserRepository: Cleared role cache key: {$key}");
            }
            
            Log::info('UserRepository: All relevant caches cleared successfully');
            
        } catch (\Exception $e) {
            Log::error('UserRepository: Failed to clear caches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get paginated users
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting paginated users', [
            'method' => 'paginate',
            'per_page' => $perPage,
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $users = $this->model->latest()->paginate($perPage);
            
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('UserRepository: Paginated users retrieved successfully', [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'per_page' => $perPage,
                'processing_time_ms' => $processingTime
            ]);

            return $users;
        } catch (\Exception $e) {
            Log::error('UserRepository: Failed to get paginated users', [
                'error' => $e->getMessage(),
                'per_page' => $perPage,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get user count
     */
    public function count(): int
    {
        $startTime = microtime(true);
        
        Log::info('UserRepository: Getting user count', [
            'method' => 'count',
            'requested_by' => auth()->id(),
            'timestamp' => now()
        ]);

        try {
            $count = $this->model->count();
            
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('UserRepository: User count retrieved successfully', [
                'count' => $count,
                'processing_time_ms' => $processingTime
            ]);

            return $count;
        } catch (\Exception $e) {
            Log::error('UserRepository: Failed to get user count', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
