<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Booking Resource
 * 
 * Transforms booking model data for API responses
 */
class BookingResource extends JsonResource
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
            'phone' => $this->phone,
            'package' => $this->package,
            'event_date' => $this->event_date ? $this->event_date->format('Y-m-d') : null,
            'event_time' => $this->event_time,
            'location' => $this->location,
            'guest_count' => $this->guest_count,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Include user data if relationship is loaded
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
        ];
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
