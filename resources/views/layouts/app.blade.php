<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Ceramic Art Dinoyo')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Master CSS - Controls all colors consistently -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    @include('layouts.navbar')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Ceramic Art Dinoyo</h5>
                    <p>Preserving traditional ceramic arts for future generations.</p>
                </div>
                <div class="col-md-6">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-map-marker-alt"></i> Kampung Keramik Dinoyo, Malang</p>
                    <p><i class="fas fa-phone"></i> +62 341 123456</p>
                    <p><i class="fas fa-envelope"></i> info@ceramicartdinoyo.com</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 Ceramic Art Dinoyo. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
