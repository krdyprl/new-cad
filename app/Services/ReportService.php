<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Information;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Report Service
 * 
 * Handles report generation and data export functionality
 */
class ReportService
{
    /**
     * Generate comprehensive report data
     */
    public function generateReport(array $filters): array
    {
        $dateFilters = $this->parseDateFilters($filters);
        
        return [
            'bookingStats' => $this->getBookingReportData($dateFilters),
            'userStats' => $this->getUserReportData($dateFilters),
            'informationStats' => $this->getInformationReportData($dateFilters),
            'filters' => $filters,
        ];
    }

    /**
     * Download report in specified format
     */
    public function downloadReport(string $type, array $filters)
    {
        $reportData = $this->generateReport($filters);
        
        return match ($type) {
            'excel' => $this->exportToExcel($reportData),
            'pdf' => $this->exportToPdf($reportData),
            'csv' => $this->exportToCsv($reportData),
            default => throw new \InvalidArgumentException("Unsupported export type: {$type}")
        };
    }

    /**
     * Parse date filters from request
     */
    private function parseDateFilters(array $filters): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : null;

        return compact('startDate', 'endDate');
    }

    /**
     * Get booking report data
     */
    private function getBookingReportData(array $dateFilters): array
    {
        $query = Booking::query();
        
        if ($dateFilters['startDate'] && $dateFilters['endDate']) {
            $query->whereBetween('created_at', [
                $dateFilters['startDate']->startOfDay(),
                $dateFilters['endDate']->endOfDay()
            ]);
        }

        $bookings = $query->with('user')->get();

        return [
            'total' => $bookings->count(),
            'by_status' => $bookings->groupBy('status')->map->count(),
            'by_package' => $bookings->groupBy('package')->map->count(),
            'revenue' => $bookings->sum('total_price'),
            'bookings' => $bookings,
        ];
    }

    /**
     * Get user report data
     */
    private function getUserReportData(array $dateFilters): array
    {
        $query = User::query();
        
        if ($dateFilters['startDate'] && $dateFilters['endDate']) {
            $query->whereBetween('created_at', [
                $dateFilters['startDate']->startOfDay(),
                $dateFilters['endDate']->endOfDay()
            ]);
        }

        $users = $query->get();

        return [
            'total' => $users->count(),
            'by_role' => $users->groupBy('role')->map->count(),
            'verified' => $users->whereNotNull('email_verified_at')->count(),
            'users' => $users,
        ];
    }

    /**
     * Get information report data
     */
    private function getInformationReportData(array $dateFilters): array
    {
        $query = Information::query();
        
        if ($dateFilters['startDate'] && $dateFilters['endDate']) {
            $query->whereBetween('created_at', [
                $dateFilters['startDate']->startOfDay(),
                $dateFilters['endDate']->endOfDay()
            ]);
        }

        $information = $query->get();

        return [
            'total' => $information->count(),
            'by_status' => $information->groupBy('status')->map->count(),
            'by_type' => $information->groupBy('type')->map->count(),
            'information' => $information,
        ];
    }

    /**
     * Export report to Excel format
     */
    private function exportToExcel(array $reportData)
    {
        // Implementation for Excel export
        // This would typically use a package like PhpSpreadsheet or Laravel Excel
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    /**
     * Export report to PDF format
     */
    private function exportToPdf(array $reportData)
    {
        // Implementation for PDF export
        // This would typically use a package like DomPDF or TCPDF
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Export report to CSV format
     */
    private function exportToCsv(array $reportData)
    {
        // Implementation for CSV export
        $filename = 'report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Type', 'Total', 'Details']);
            
            // Booking data
            fputcsv($file, ['Bookings', $reportData['bookingStats']['total'], json_encode($reportData['bookingStats']['by_status'])]);
            
            // User data
            fputcsv($file, ['Users', $reportData['userStats']['total'], json_encode($reportData['userStats']['by_role'])]);
            
            // Information data
            fputcsv($file, ['Information', $reportData['informationStats']['total'], json_encode($reportData['informationStats']['by_status'])]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
