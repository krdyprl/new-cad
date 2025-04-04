<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Information Resource
 * 
 * Transforms information model data for API responses
 */
class InformationResource extends JsonResource
{
    /**
     * Transform the resource into an array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image ? asset($this->image) : null,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Include author data if relationship is loaded
            'author' => $this->whenLoaded('author', function () {
                return new UserResource($this->author);
            }),
        ];
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'published' => 'Dipublikasikan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get human-readable type label
     */
    private function getTypeLabel(): string
    {
        return match ($this->type) {
            'news' => 'Berita',
            'information' => 'Informasi',
            default => ucfirst($this->type),
        };
    }
}
