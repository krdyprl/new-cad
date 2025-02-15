<?php

namespace App\Contracts;

/**
 * Dashboard Service Interface
 * 
 * Defines the contract for dashboard-related operations
 * Following Interface Segregation Principle
 */
interface DashboardServiceInterface
{
    /**
     * Get all dashboard data
     */
    public function getDashboardData(): array;

    /**
     * Generate reports based on filters
     */
    public function generateReports(array $filters = []): array;

    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array;

    /**
     * Get recent activities
     */
    public function getRecentActivities(): array;

    /**
     * Get chart data for dashboard
     */
    public function getChartData(): array;
}
