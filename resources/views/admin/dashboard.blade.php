@extends('admin.layout')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="admin-title">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
        </h2>
        <div class="text-muted">
            <i class="fas fa-calendar me-1"></i>{{ date('d F Y') }}
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-users">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $totalUsers ?? 2 }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card stat-card-bookings">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $totalBookings ?? 7 }}</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card stat-card-pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $pendingBookings ?? 1 }}</div>
                    <div class="stat-label">Pending Bookings</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $totalInformation ?? 5 }}</div>
                    <div class="stat-label">Total Information</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-pie me-2"></i>Booking Status Chart</h5>
                </div>
                <div class="chart-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Monthly Bookings</h5>
                </div>
                <div class="chart-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');
    
    // Check if Chart.js is available
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js is available');
        
        // Simple status chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            try {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Confirmed', 'Completed'],
                        datasets: [{
                            data: [{{ $pendingBookings ?? 1 }}, {{ $confirmedBookings ?? 4 }}, {{ $completedBookings ?? 2 }}],
                            backgroundColor: ['#D4A574', '#cab491', '#8b7355'],
                            borderWidth: 3,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
                console.log('Status chart created successfully');
            } catch (error) {
                console.error('Error creating status chart:', error);
            }
        }
        
        // Simple monthly chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            try {
                const monthlyData = @json($monthlyBookingsData ?? []);
                const monthlyValues = [];
                for (let i = 1; i <= 12; i++) {
                    monthlyValues.push(monthlyData[i] || 0);
                }
                
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Bookings',
                            data: monthlyValues,
                            borderColor: '#cab491',
                            backgroundColor: 'rgba(202, 180, 145, 0.2)',
                            borderWidth: 3,
                            pointBackgroundColor: '#8b7355',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#8b7355'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(139, 115, 85, 0.1)'
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#8b7355'
                                }
                            }
                        }
                    }
                });
                console.log('Monthly chart created successfully');
            } catch (error) {
                console.error('Error creating monthly chart:', error);
            }
        }
    } else {
        console.error('Chart.js is not available');
        alert('Chart.js tidak dimuat. Periksa koneksi internet.');
    }
});
</script>
@endsection
