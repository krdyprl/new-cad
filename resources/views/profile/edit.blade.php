<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Profile') }} - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
      <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
          .page-header {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            padding: 60px 0 80px 0; /* Reduced top padding since navbar is fixed */
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        
        .profile-section {
            padding: 60px 0;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .profile-card-header {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .profile-card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #cab491;
            box-shadow: 0 0 0 0.2rem rgba(202, 180, 145, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2rem;
            color: #cab491;
        }
        
        .profile-actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .profile-actions .btn {
            margin: 0 10px;
        }
        
        .danger-zone {
            border-top: 1px solid #dee2e6;
            padding-top: 30px;
            margin-top: 30px;
        }
        
        .danger-zone h4 {
            color: #dc3545;
            margin-bottom: 15px;
        }
          .navbar-brand img {
            height: 65px;
        }
        
        @media (max-width: 768px) {
            .profile-actions .btn {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>
    @include('layouts.navbar')

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-user-circle"></i> Profil Saya</h1>
            <p class="lead">Kelola informasi akun Anda</p>
        </div>
    </div>

    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Profile Info Card -->
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>{{ $user->name }}</h3>
                            <p class="mb-0">{{ $user->email }}</p>
                            <small>Bergabung sejak {{ $user->created_at->format('d M Y') }}</small>
                        </div>
                        
                        <div class="profile-card-body">
                            <!-- Success Message -->
                            @if (session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Profile Update Form -->
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-user"></i> Nama Lengkap
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">
                                        <i class="fas fa-phone"></i> Nomor Telepon
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone ?? '') }}" 
                                           placeholder="Contoh: 08123456789">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="profile-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('frontend.home') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>

                            <!-- Change Password Section -->
                            <div class="danger-zone">
                                <h4><i class="fas fa-key"></i> Ubah Password</h4>
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="change_password" value="1">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="current_password">Password Saat Ini</label>
                                                <input type="password" 
                                                       class="form-control @error('current_password') is-invalid @enderror" 
                                                       id="current_password" 
                                                       name="current_password">
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Password Baru</label>
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation">
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Ubah Password
                                    </button>
                                </form>
                            </div>

                            <!-- Delete Account Section -->
                            <div class="danger-zone">
                                <h4><i class="fas fa-trash"></i> Zona Berbahaya</h4>
                                <p class="text-muted">Tindakan berikut tidak dapat dibatalkan. Harap berhati-hati.</p>
                                
                                <form method="POST" action="{{ route('profile.destroy') }}" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus Akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    </section>
    <!--========================Footer============================-->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 footer-info">
                        <h3>CAD (Ceramic Art Dinoyo)</h3>
                        <p>Kampung Keramik Dinoyo adalah area di Kota Malang, Jawa Timur, yang terkenal dengan kerajinan keramik dan tembikar. Tempat ini menjadi pusat produksi keramik tradisional yang telah 				berkembang sejak lama.</p>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>{{ __('messages.useful_links') }}</h4>
                        <ul>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.home') }}">Home</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.about') }}">About us</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.catalog') }}">Catalog</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.booking') }}">Booking</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.contact') }}">Contact</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-5 footer-contact">
                        <h4>{{ __('messages.contact_info') }}</h4>
                        <p>
                            {{ __('messages.address') }}<br>
                            <strong>{{ __('messages.phone') }}:</strong> +62 341 123456<br>
                            <strong>{{ __('messages.email') }}:</strong> info@ceramicartdinoyo.com<br>
                        </p>
                        <div class="social-links">
                        <a href="https://www.instagram.com/dinoyoceramic" class="instagram" target="_blank">
                            <i class="fab fa-instagram"></i>@dinoyoceramic
                        </a>
                        </div>
                    </div>

                    <!-- Adjusted Google Maps iframe to be more proportional -->
                    <div class="col-lg-3 col-md-6 footer-map">
                        <h4 class="text-center mb-4">Lokasi Kami</h4>
                        <div class="map-container">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.2742486068647!2d112.61175506958969!3d-7.941624325211181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629086c64985b%3A0x5027a76e356bda0!2sKampung%20Wisata%20Keramik%20Dinoyo!5e0!3m2!1sen!2sid!4v1636123456789!5m2!1sen!2sid"
                                style="border:0; width: 100%; height: 300px; border-radius: 8px;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
        <div class="copyright">
            &copy; Copyright <strong>Ceramic Art Dinoyo</strong>. All Rights Reserved
        </div>
        </div>
</footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
