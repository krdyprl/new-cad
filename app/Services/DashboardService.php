<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\Information;
use Illuminate\Support\Collection;

/**
 * Dashboard Service
 * 
 * Handles dashboard-related business logic and data aggregation
 */
class DashboardService
{
    private const RECENT_ITEMS_LIMIT = 5;
    private const CURRENT_YEAR = 2024; // Can be made dynamic

    /**
     * Get all dashboard data in one method
     */
    public function getDashboardData(): array
    {
        return [
            ...$this->getBasicStatistics(),
            ...$this->getRecentActivities(),
            ...$this->getChartData()
        ];
    }

    /**
     * Get basic dashboard statistics
     */
    private function getBasicStatistics(): array
    {
        return [
            'totalUsers' => $this->getTotalUsers(),
            'totalBookings' => $this->getTotalBookings(),
            'totalInformation' => $this->getTotalInformation(),
            'pendingBookings' => $this->getPendingBookings(),
            'confirmedBookings' => $this->getConfirmedBookings(),
            'completedBookings' => $this->getCompletedBookings(),
        ];
    }

    /**
     * Get recent activities data
     */
    private function getRecentActivities(): array
    {
        return [
            'recentBookings' => $this->getRecentBookings(),
            'recentUsers' => $this->getRecentUsers(),
        ];
    }

    /**
     * Get chart data for dashboard visualizations
     */
    private function getChartData(): array
    {
        return [
            'monthlyBookingsData' => $this->getMonthlyBookingsData(),
            'packageStats' => $this->getPackageStatistics(),
        ];
    }

    /**
     * Get total number of users
     */
    private function getTotalUsers(): int
    {
        return User::count();
    }

    /**
     * Get total number of bookings
     */
    private function getTotalBookings(): int
    {
        return Booking::count();
    }

    /**
     * Get total number of information entries
     */
    private function getTotalInformation(): int
    {
        return Information::count();
    }

    /**
     * Get number of pending bookings
     */
    private function getPendingBookings(): int
    {
        return Booking::where('status', 'pending')->count();
    }

    /**
     * Get number of confirmed bookings
     */
    private function getConfirmedBookings(): int
    {
        return Booking::where('status', 'confirmed')->count();
    }

    /**
     * Get number of completed bookings
     */
    private function getCompletedBookings(): int
    {
        return Booking::where('status', 'completed')->count();
    }

    /**
     * Get recent bookings with user information
     */
    private function getRecentBookings(): Collection
    {
        return Booking::with('user')
            ->orderBy('created_at', 'desc')
            ->take(self::RECENT_ITEMS_LIMIT)
            ->get();
    }

    /**
     * Get recent users
     */
    private function getRecentUsers(): Collection
    {
        return User::orderBy('created_at', 'desc')
            ->take(self::RECENT_ITEMS_LIMIT)
            ->get();
    }

    /**
     * Get monthly booking statistics for chart
     */
    private function getMonthlyBookingsData(): array
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = Booking::whereMonth('created_at', $month)
                ->whereYear('created_at', self::CURRENT_YEAR)
                ->count();
        }

        return $monthlyData;
    }

    /**
     * Get package popularity statistics
     */
    private function getPackageStatistics(): array
    {
        $packageStats = [];
        
        $packages = Booking::selectRaw('package, COUNT(*) as count')
            ->groupBy('package')
            ->get();
        
        foreach ($packages as $package) {
            $packageStats[$package->package] = $package->count;
        }

        return $packageStats;
    }
}
