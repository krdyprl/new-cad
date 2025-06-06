<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.about') }} - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Master CSS - Controls all colors consistently -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
      <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .page-header {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            padding: 120px 0 80px 0; /* Extra top padding for fixed navbar */
            text-align: center;
        }
            padding: 100px 0;
            text-align: center;
      
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .info-section {
            padding: 60px 0;
            background: #f7f7f7;
        }
        
        .info-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 30px;
            transition: transform 0.3s ease;
        }
        
        .info-box:hover {
            transform: translateY(-5px);
        }
        
        .info-box h4 {
            color: #cab491;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 15px;
        }
        
        .sidebar-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-box h5 {
            color: #cab491;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="page-header">
        <div class="container">
            <h1>About Kampung Keramik Dinoyo</h1>
            <p>Kampung Keramik Dinoyo adalah sebuah kawasan di Kota Malang, Jawa Timur, yang terkenal sebagai sentra pengrajin keramik.</p>
        </div>
    </div>

    <section class="info-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="info-box">
                        <h4>Sejarah Kampung Keramik Dinoyo</h4>
                        <p>Kampung Keramik Dinoyo memiliki sejarah panjang dalam pembuatan keramik, bahkan sejak zaman Kerajaan Kanjuruhan. Pengrajin di kawasan ini telah mewariskan keahlian membuat keramik secara turun-temurun.</p>
                        <p>Pada masa kolonial Belanda, industri keramik di Dinoyo mulai berkembang pesat. Teknik-teknik baru diperkenalkan yang kemudian disesuaikan dengan kearifan lokal, menciptakan gaya keramik khas Dinoyo yang unik.</p>
                    </div>
                    
                    <div class="info-box">
                        <h4>Teknik Pembuatan Keramik</h4>
                        <p>Para pengrajin di Kampung Keramik Dinoyo menggunakan berbagai teknik tradisional yang telah diwariskan secara turun-temurun:</p>
                        <ul>
                            <li><strong>Teknik Putar:</strong> Membentuk keramik menggunakan roda putar tradisional</li>
                            <li><strong>Teknik Pijit:</strong> Membentuk keramik dengan tangan secara langsung</li>
                            <li><strong>Teknik Cetak:</strong> Menggunakan cetakan untuk bentuk-bentuk khusus</li>
                            <li><strong>Teknik Glasir:</strong> Proses pewarnaan dan finishing keramik</li>
                        </ul>
                    </div>
                    
                    <div class="info-box">
                        <h4>Produk Unggulan</h4>
                        <p>Kampung Keramik Dinoyo menghasilkan berbagai produk keramik berkualitas tinggi:</p>
                        <ul>
                            <li>Genteng dan bata untuk konstruksi</li>
                            <li>Pot dan vas hias untuk dekorasi</li>
                            <li>Peralatan dapur dan makan</li>
                            <li>Keramik artistik dan suvenir</li>
                            <li>Ubin dan keramik lantai</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="sidebar-box">
                        <h5>{{ __('messages.opening_hours') }}</h5>
                        <ul class="list-unstyled">
                            <li><strong>Senin - Jumat:</strong> 08:00 - 17:00</li>
                            <li><strong>Sabtu:</strong> 08:00 - 15:00</li>
                            <li><strong>Minggu:</strong> 09:00 - 14:00</li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-box">
                        <h5>{{ __('messages.location') }}</h5>
                        <p>{{ __('messages.address') }}</p>
                        <p><strong>{{ __('messages.phone') }}:</strong> +62 341 123456</p>
                        <p><strong>{{ __('messages.email') }}:</strong> info@ceramicartdinoyo.com</p>
                        <a href="{{ route('frontend.chatbot') }}" class="btn" style="background: #cab491; color: white;">{{ __('messages.chatbot') }}</a>
                    </div>
                    
                    <div class="sidebar-box">
                        <h5>Fasilitas</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Area Parkir</li>
                            <li><i class="fas fa-check text-success me-2"></i>Toilet Umum</li>
                            <li><i class="fas fa-check text-success me-2"></i>Workshop Area</li>
                            <li><i class="fas fa-check text-success me-2"></i>Galeri Produk</li>
                            <li><i class="fas fa-check text-success me-2"></i>Kantin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
