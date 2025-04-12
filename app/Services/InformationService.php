<?php

namespace App\Services;

use App\Models\Information;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * Information Service
 * 
 * Handles information/content management business logic
 */
class InformationService
{
    private const PAGINATION_LIMIT = 10;
    private const IMAGE_UPLOAD_PATH = 'img/information';
    private const ALLOWED_IMAGE_TYPES = ['jpeg', 'png', 'jpg', 'gif'];
    private const MAX_IMAGE_SIZE = 2048; // KB

    /**
     * Get paginated information entries
     */
    public function getPaginatedInformation(): LengthAwarePaginator
    {
        return Information::orderBy('created_at', 'desc')
            ->paginate(self::PAGINATION_LIMIT);
    }

    /**
     * Create new information entry
     */
    public function create(array $data): Information
    {
        $processedData = $this->processInformationData($data);
        
        return Information::create($processedData);
    }

    /**
     * Update existing information entry
     */
    public function update(Information $information, array $data): bool
    {
        $processedData = $this->processInformationData($data, $information);
        
        return $information->update($processedData);
    }

    /**
     * Delete information entry
     */
    public function delete(Information $information): bool
    {
        $this->deleteImageFile($information->image);
        
        return $information->delete();
    }

    /**
     * Process information data before saving
     */
    private function processInformationData(array $data, ?Information $existing = null): array
    {
        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if updating
            if ($existing && $existing->image) {
                $this->deleteImageFile($existing->image);
            }
            
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        // Generate slug
        if (isset($data['title'])) {
            if (!$existing || $data['title'] !== $existing->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }
        }

        // Set author
        if (!$existing) {
            $data['author_id'] = auth()->id();
        }

        // Handle publication status
        $data = $this->handlePublicationStatus($data, $existing);

        return $data;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(UploadedFile $image): string
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path(self::IMAGE_UPLOAD_PATH), $imageName);
        
        return self::IMAGE_UPLOAD_PATH . '/' . $imageName;
    }

    /**
     * Delete image file from storage
     */
    private function deleteImageFile(?string $imagePath): void
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
    }

    /**
     * Generate unique slug for information
     */
    private function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Information::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Handle publication status and timestamp
     */
    private function handlePublicationStatus(array $data, ?Information $existing): array
    {
        if (isset($data['status'])) {
            if ($data['status'] === 'published') {
                // Set published_at if not already published
                if (!$existing || $existing->status !== 'published') {
                    $data['published_at'] = now();
                }
            } elseif ($data['status'] === 'draft') {
                $data['published_at'] = null;
            }
        }

        return $data;
    }

    /**
     * Get published information
     */
    public function getPublishedInformation(): \Illuminate\Database\Eloquent\Collection
    {
        return Information::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get information by type
     */
    public function getInformationByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return Information::where('type', $type)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
