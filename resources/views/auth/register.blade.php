<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Daftar - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Global Styles -->
    <link href="{{ asset('css/custom-global.css') }}" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <!-- Master CSS - Controls all colors consistently -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
      <style>        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }        .auth-container {
            padding: 140px 0 60px 0; /* Increased top padding for bigger navbar */
        }
        
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .auth-header h2 {
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .auth-header p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #cab491;
            box-shadow: 0 0 0 0.2rem rgba(202, 180, 145, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 500;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.4);
            color: white;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 30px;
        }
        
        .auth-links a {
            color: #cab491;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            color: #8b7355;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-card">
                        <div class="auth-header">
                            <h2>Buat Akun Baru</h2>
                            <p>Bergabunglah dengan komunitas Ceramic Art Dinoyo</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" required 
                                       placeholder="contoh: 08123456789">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <div class="password-requirements">
                                    Password minimal 8 karakter
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-register">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </button>
                        </form>

                        <div class="auth-links">
                            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                            <a href="{{ route('frontend.home') }}">← Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
