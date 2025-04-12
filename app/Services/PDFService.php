<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

/**
 * PDF Service
 * 
 * Handles PDF generation operations following Single Responsibility Principle
 * Separates PDF concerns from other business logic
 */
class PDFService
{
    /**
     * Generate booking invoice PDF with enhanced logging and error handling
     */
    public function generateBookingInvoice(Booking $booking): string
    {
        $startTime = microtime(true);
        
        Log::info('PDFService: Invoice generation started', [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_id,
            'customer_email' => $booking->email,
            'total_amount' => $booking->total,
            'timestamp' => now(),
            'initiated_by' => auth()->id()
        ]);

        try {
            // Check if PDF already exists and is valid
            if ($booking->pdf_file && $this->pdfExists($booking->pdf_file)) {
                Log::info('PDFService: Existing PDF found, returning cached version', [
                    'booking_id' => $booking->id,
                    'pdf_file' => $booking->pdf_file,
                    'file_size' => $this->getPdfFileSize($booking->pdf_file)
                ]);
                
                return $this->getPdfContent($booking->pdf_file);
            }

            Log::info('PDFService: Generating new PDF invoice', [
                'booking_id' => $booking->id,
                'reason' => $booking->pdf_file ? 'existing_pdf_invalid' : 'no_existing_pdf'
            ]);

            // Generate new PDF
            $pdfContent = $this->createInvoicePDF($booking);
            
            // Store PDF file
            $fileName = $this->storePDF($booking, $pdfContent);
            
            // Update booking with PDF file name
            $booking->update(['pdf_file' => $fileName]);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('PDFService: Invoice PDF generated successfully', [
                'booking_id' => $booking->id,
                'pdf_file' => $fileName,
                'file_size' => strlen($pdfContent),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Log activity on booking
            if (method_exists($booking, 'logActivity')) {
                $booking->logActivity('pdf_invoice_generated', [
                    'pdf_file' => $fileName,
                    'file_size' => strlen($pdfContent),
                    'processing_time_ms' => $processingTime,
                    'service_method' => 'PDFService::generateBookingInvoice'
                ]);
            }
            
            return $pdfContent;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('PDFService: PDF generation failed', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            // Log activity on booking for failed generation
            if (method_exists($booking, 'logActivity')) {
                $booking->logActivity('pdf_invoice_generation_failed', [
                    'error' => $e->getMessage(),
                    'processing_time_ms' => $processingTime,
                    'service_method' => 'PDFService::generateBookingInvoice'
                ]);
            }
            
            throw new \Exception('Failed to generate PDF invoice: ' . $e->getMessage());
        }
    }

    /**
     * Create PDF content from booking data with enhanced logging
     */
    private function createInvoicePDF(Booking $booking): string
    {
        $startTime = microtime(true);
        
        Log::info('PDFService: Creating PDF content', [
            'booking_id' => $booking->id,
            'template' => 'booking-invoice'
        ]);

        try {
            $data = [
                'booking' => $booking,
                'company' => [
                    'name' => 'Ceramic Art Dinoyo',
                    'address' => 'Kampung Keramik Dinoyo, Malang',
                    'phone' => '+62 341 123456',
                    'email' => 'info@ceramicartdinoyo.com'
                ],
                'generated_at' => now()
            ];

            $pdf = Pdf::loadView('booking-invoice', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $pdfContent = $pdf->output();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('PDFService: PDF content created successfully', [
                'booking_id' => $booking->id,
                'content_size' => strlen($pdfContent),
                'processing_time_ms' => $processingTime
            ]);
            
            return $pdfContent;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('PDFService: Failed to create PDF content', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'processing_time_ms' => $processingTime
            ]);
            
            throw $e;
        }
    }

    /**
     * Store PDF file to storage with enhanced logging
     */
    private function storePDF(Booking $booking, string $pdfContent): string
    {
        $startTime = microtime(true);
        
        Log::info('PDFService: Storing PDF file', [
            'booking_id' => $booking->id,
            'content_size' => strlen($pdfContent)
        ]);

        try {
            $fileName = 'invoice-' . $booking->booking_id . '-' . time() . '.pdf';
            $path = 'invoices/' . $fileName;
            
            // Ensure directory exists
            $directory = dirname(storage_path('app/public/' . $path));
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            Storage::disk('public')->put($path, $pdfContent);
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            // Verify file was stored successfully
            $storedSize = Storage::disk('public')->size($path);
            
            Log::info('PDFService: PDF file stored successfully', [
                'booking_id' => $booking->id,
                'file_name' => $fileName,
                'file_path' => $path,
                'original_size' => strlen($pdfContent),
                'stored_size' => $storedSize,
                'processing_time_ms' => $processingTime
            ]);
            
            return $fileName;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('PDFService: Failed to store PDF file', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'processing_time_ms' => $processingTime
            ]);
            
            throw new \Exception('Failed to store PDF file: ' . $e->getMessage());
        }
    }

    /**
     * Check if PDF file exists with logging
     */
    private function pdfExists(string $fileName): bool
    {
        $exists = Storage::disk('public')->exists('invoices/' . $fileName);
        
        Log::debug('PDFService: PDF existence check', [
            'file_name' => $fileName,
            'exists' => $exists
        ]);
        
        return $exists;
    }

    /**
     * Get existing PDF content with logging
     */
    private function getPdfContent(string $fileName): string
    {
        Log::info('PDFService: Retrieving existing PDF content', [
            'file_name' => $fileName
        ]);

        try {
            $content = Storage::disk('public')->get('invoices/' . $fileName);
            
            Log::info('PDFService: PDF content retrieved successfully', [
                'file_name' => $fileName,
                'content_size' => strlen($content)
            ]);
            
            return $content;

        } catch (\Exception $e) {
            Log::error('PDFService: Failed to retrieve PDF content', [
                'file_name' => $fileName,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to retrieve PDF content: ' . $e->getMessage());
        }
    }

    /**
     * Get PDF file size
     */
    private function getPdfFileSize(string $fileName): int
    {
        try {
            if ($this->pdfExists($fileName)) {
                return Storage::disk('public')->size('invoices/' . $fileName);
            }
            return 0;
        } catch (\Exception $e) {
            Log::warning('PDFService: Failed to get PDF file size', [
                'file_name' => $fileName,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Delete PDF file with enhanced logging
     */
    public function deletePDF(string $fileName, ?int $bookingId = null): bool
    {
        Log::info('PDFService: Deleting PDF file', [
            'file_name' => $fileName,
            'booking_id' => $bookingId
        ]);

        try {
            if ($this->pdfExists($fileName)) {
                $fileSize = $this->getPdfFileSize($fileName);
                $result = Storage::disk('public')->delete('invoices/' . $fileName);
                
                if ($result) {
                    Log::info('PDFService: PDF file deleted successfully', [
                        'file_name' => $fileName,
                        'booking_id' => $bookingId,
                        'deleted_file_size' => $fileSize
                    ]);
                } else {
                    Log::warning('PDFService: PDF file deletion failed', [
                        'file_name' => $fileName,
                        'booking_id' => $bookingId
                    ]);
                }
                
                return $result;
            }
            
            Log::info('PDFService: PDF file already does not exist', [
                'file_name' => $fileName,
                'booking_id' => $bookingId
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('PDFService: Error deleting PDF file', [
                'file_name' => $fileName,
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Get PDF file path for download with enhanced logging
     */
    public function getPDFPath(string $fileName): ?string
    {
        Log::debug('PDFService: Getting PDF file path', [
            'file_name' => $fileName
        ]);

        try {
            if ($this->pdfExists($fileName)) {
                $path = storage_path('app/public/invoices/' . $fileName);
                
                Log::info('PDFService: PDF file path retrieved', [
                    'file_name' => $fileName,
                    'full_path' => $path,
                    'file_size' => $this->getPdfFileSize($fileName)
                ]);
                
                return $path;
            }
            
            Log::warning('PDFService: PDF file not found for path retrieval', [
                'file_name' => $fileName
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error('PDFService: Error getting PDF file path', [
                'file_name' => $fileName,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Get PDF statistics (for admin dashboard)
     */
    public function getPDFStats(): array
    {
        Log::info('PDFService: Getting PDF statistics');

        try {
            $invoicesPath = 'invoices/';
            $files = Storage::disk('public')->files($invoicesPath);
            
            $totalFiles = count($files);
            $totalSize = 0;
            
            foreach ($files as $file) {
                $totalSize += Storage::disk('public')->size($file);
            }
            
            $stats = [
                'total_files' => $totalFiles,
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'average_file_size_kb' => $totalFiles > 0 ? round(($totalSize / $totalFiles) / 1024, 2) : 0,
                'generated_at' => now()
            ];
            
            Log::info('PDFService: PDF statistics generated', $stats);
            
            return $stats;

        } catch (\Exception $e) {
            Log::error('PDFService: Error getting PDF statistics', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'total_files' => 0,
                'total_size_bytes' => 0,
                'total_size_mb' => 0,
                'average_file_size_kb' => 0,
                'error' => $e->getMessage(),
                'generated_at' => now()
            ];
        }
    }
}
