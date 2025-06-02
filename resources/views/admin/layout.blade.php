<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Master CSS - Controls all colors consistently -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
    * {
        box-sizing: border-box;
    }
    
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .row {
        margin: 0 !important;
    }
    
    .col-md-3, .col-lg-2, .col-md-9, .col-lg-10 {
        padding: 0 !important;
    }
    
    .admin-sidebar {
        background: linear-gradient(180deg, #8B4513, #A0522D);
        min-height: 100vh;
        padding: 0;
        margin: 0;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        position: relative;
    }
    
    .admin-sidebar .nav-link {
        color: rgba(255,255,255,0.8) !important;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: all 0.3s ease;
    }
    
    .admin-sidebar .nav-link:hover,
    .admin-sidebar .nav-link.active {
        color: white !important;
        background-color: rgba(255,255,255,0.1);
        border-left: 4px solid #D2691E;
    }
    
    .admin-sidebar .nav-link i {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
    }
    
    .admin-content {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }
    
    .admin-content > .dashboard-content,
    .admin-content > div:not(.alert) {
        padding: 2rem;
        margin: 0;
    }
    
    .admin-content .alert {
        margin: 1rem 2rem;
    }
    
    .admin-brand {
        background-color: rgba(0,0,0,0.2);
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid rgba(255,255,255,0.1);
    }
    
    .admin-brand h4 {
        color: white;
        margin: 0;
        font-weight: 600;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .card-header {
        background: linear-gradient(135deg, #8B4513, #A0522D);
        color: white;
        border-bottom: none;
        border-radius: 15px 15px 0 0 !important;
        font-weight: 600;
    }
    
    .btn-primary {
        background-color: #8B4513;
        border-color: #8B4513;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #A0522D;
        border-color: #A0522D;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .table th {
        background-color: #F5F5DC;
        color: #654321;
        font-weight: 600;
        border-top: none;
    }
    
    .alert {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    /* Admin Dashboard Specific Styles */
    .admin-title {
        color: #654321;
        font-weight: 700;
        margin: 0;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #cab491, #8b7355);
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }
    
    .stat-card .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #cab491, #8b7355);
        box-shadow: 0 8px 20px rgba(202, 180, 145, 0.3);
    }
    
    .stat-card .stat-content .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #654321;
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-card .stat-content .stat-label {
        color: #8b7355;
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Specific stat card colors */
    .stat-card-users .stat-icon {
        background: linear-gradient(135deg, #cab491, #a68b57);
    }
    
    .stat-card-bookings .stat-icon {
        background: linear-gradient(135deg, #8b7355, #654321);
    }
    
    .stat-card-pending .stat-icon {
        background: linear-gradient(135deg, #D4A574, #cab491);
    }
    
    .stat-card-info .stat-icon {
        background: linear-gradient(135deg, #a68b57, #8b7355);
    }
    
    .chart-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        border: none;
    }
    
    .chart-header {
        background: linear-gradient(135deg, #cab491, #8b7355);
        color: white;
        padding: 1.5rem 2rem;
        border-bottom: none;
        margin: 0;
    }
    
    .chart-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .chart-body {
        padding: 2rem;
        position: relative;
        height: 350px;
    }
    
    .chart-body canvas {
        max-height: 300px !important;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .admin-sidebar {
            min-height: auto;
        }
        
        .admin-content {
            padding: 0;
            margin: 0;
        }
        
        .admin-content > .dashboard-content,
        .admin-content > div:not(.alert) {
            padding: 1rem;
        }
        
        .admin-content .alert {
            margin: 0.5rem 1rem;
        }
        
        .stat-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card .stat-content .stat-number {
            font-size: 2rem;
        }
        
        .chart-body {
            padding: 1rem;
            height: 300px;
        }
        
        .chart-body canvas {
            max-height: 250px !important;
        }
    }
    
    /* Loading animation */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .stat-card:hover .stat-icon {
        animation: pulse 0.6s ease-in-out;
    }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0 m-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="admin-brand">
                    <h4><i class="fas fa-palette me-2"></i>Admin Panel</h4>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                        <i class="fas fa-users"></i>Pengguna
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings') }}">
                        <i class="fas fa-calendar-check"></i>Booking
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.information*') ? 'active' : '' }}" href="{{ route('admin.information') }}">
                        <i class="fas fa-info-circle"></i>Informasi
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products') }}">
                        <i class="fas fa-box"></i>Produk
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar"></i>Laporan
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                        <i class="fas fa-cog"></i>Pengaturan
                    </a>
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>Keluar
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>
