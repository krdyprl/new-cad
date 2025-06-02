@extends('admin.layout')

@section('title', 'Laporan & Statistik')

@section('content')
<div class="row">
    <!-- Revenue Cards -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-success-gradient">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-info-gradient">
                <i class="fas fa-calendar-month"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Revenue Bulan Ini</div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning-gradient">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">{{ $popularPackages->first()->count ?? 0 }}</div>
            <div class="stat-label">Paket Terpopuler</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Booking Chart -->
    <div class="col-xl-8">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-bar"></i> Booking per Bulan {{ date('Y') }}</h5>
            <canvas id="monthlyChart" height="300"></canvas>
        </div>
    </div>

    <!-- Package Popularity -->
    <div class="col-xl-4">
        <div class="chart-container">
            <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Popularitas Paket</h5>
            <canvas id="packageChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <!-- Popular Packages Table -->
    <div class="col-xl-6">
        <div class="table-container">
            <h5 class="mb-3"><i class="fas fa-trophy"></i> Paket Terpopuler</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Paket</th>
                            <th>Total Booking</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($popularPackages as $index => $package)
                        <tr>
                            <td>
                                @if($index == 0)
                                    <i class="fas fa-crown text-warning"></i> 1
                                @elseif($index == 1)
                                    <i class="fas fa-medal text-secondary"></i> 2
                                @elseif($index == 2)
                                    <i class="fas fa-medal text-warning"></i> 3
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>{{ ucfirst($package->package) }}</td>
                            <td>{{ $package->count }}</td>
                            <td>
                                @php
                                    $percentage = ($package->count / $popularPackages->sum('count')) * 100;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Download Reports -->
    <div class="col-xl-6">
        <div class="table-container">
            <h5 class="mb-3"><i class="fas fa-download"></i> Download Laporan</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.reports.download', 'bookings') }}" class="btn btn-primary">
                    <i class="fas fa-file-excel"></i> Download Laporan Booking (Excel)
                </a>
                <a href="{{ route('admin.reports.download', 'users') }}" class="btn btn-success">
                    <i class="fas fa-file-pdf"></i> Download Laporan Users (PDF)
                </a>
                <a href="{{ route('admin.reports.download', 'revenue') }}" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Download Laporan Revenue (Excel)
                </a>
                <button class="btn btn-warning" onclick="printReport()">
                    <i class="fas fa-print"></i> Print Laporan Dashboard
                </button>
            </div>
            
            <!-- Filter Reports -->
            <div class="mt-4">
                <h6><i class="fas fa-filter"></i> Filter Laporan</h6>
                <form action="{{ route('admin.reports') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter Data
                        </button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Monthly Booking Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyData = @json(array_values($monthlyBookingData));
const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Jumlah Booking',
            data: monthlyData,
            backgroundColor: 'rgba(202, 180, 145, 0.8)',
            borderColor: 'rgba(202, 180, 145, 1)',
            borderWidth: 1
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
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Package Popularity Chart
const packageCtx = document.getElementById('packageChart').getContext('2d');
const packageLabels = @json($popularPackages->pluck('package'));
const packageData = @json($popularPackages->pluck('count'));

new Chart(packageCtx, {
    type: 'doughnut',
    data: {
        labels: packageLabels,
        datasets: [{
            data: packageData,
            backgroundColor: [
                'rgba(202, 180, 145, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                'rgba(202, 180, 145, 1)',
                'rgba(40, 167, 69, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Print function
function printReport() {
    window.print();
}
</script>
@endpush
@endsection
