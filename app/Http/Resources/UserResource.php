<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Resource
 * 
 * Transforms user model data for API responses
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'role_label' => $this->getRoleLabel(),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'is_verified' => !is_null($this->email_verified_at),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Include booking count if needed
            'bookings_count' => $this->whenCounted('bookings'),
        ];
    }

    /**
     * Get human-readable role label
     */
    private function getRoleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'user' => 'Pengguna',
            default => ucfirst($this->role),
        };
    }
}
