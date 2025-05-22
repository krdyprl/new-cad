<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Login - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Global Styles -->
    <link href="{{ asset('css/custom-global.css') }}" rel="stylesheet">
    
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
            max-width: 450px;
            margin: 0 auto;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .auth-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .auth-header p {
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-login {
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
        
        .btn-login:hover {
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
        
        .form-check {
            margin: 20px 0;
        }
        
        .form-check-input:checked {
            background-color: #cab491;
            border-color: #cab491;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(202, 180, 145, 0.25);
            border-color: #cab491;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="auth-card">
                        <div class="auth-header">
                            <h2>Masuk ke Akun Anda</h2>
                            <p>Selamat datang kembali di Ceramic Art Dinoyo</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Masuk
                            </button>
                        </form>

                        <div class="auth-links">
                            <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
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
