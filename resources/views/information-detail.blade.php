<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $article->title }} - Ceramic Art Dinoyo</title>
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
        
        .article-header {
            padding: 140px 0 60px 0; /* Increased top padding for bigger navbar */
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
        }
        
        .article-content {
            padding: 60px 0;
        }
        
        .article-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .article-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .article-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2c3e50;
        }
        
        .related-articles {
            background: #f8f9fa;
            padding: 60px 0;
        }
        
        .related-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
        }
        
        .related-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            margin-bottom: 30px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.4);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="article-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('frontend.information') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Portal Berita
                    </a>
                    <h1 class="display-4 mb-3">{{ $article->title }}</h1>
                    <div class="article-meta-header d-flex align-items-center text-white-50">
                        <span class="me-4"><i class="fas fa-calendar me-2"></i>{{ $article->created_at->format('d F Y') }}</span>
                        <span><i class="fas fa-user me-2"></i>Admin Keramik Dinoyo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="article-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    @if($article->hasValidImage())
                        <div class="article-image-container mb-4">
                            <img src="{{ $article->getImageUrl() }}" alt="{{ $article->title }}" class="article-image">
                        </div>
                    @else
                        <div class="article-image-placeholder mb-4">
                            <div class="article-image" style="background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <i class="fas fa-newspaper fa-3x"></i>
                            </div>
                        </div>
                    @endif
                    
                    <div class="article-meta">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Tanggal Publikasi:</strong><br>
                                {{ $article->created_at->format('d F Y, H:i') }} WIB
                            </div>
                            <div class="col-md-6">
                                <strong>Kategori:</strong><br>
                                Berita Keramik Dinoyo
                            </div>
                        </div>
                    </div>
                    
                    <div class="article-body">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                    
                    <div class="mt-5">
                        <a href="{{ route('frontend.information') }}" class="back-btn">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Portal Berita
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($relatedArticles->count() > 0)
    <section class="related-articles">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="text-center mb-5">Artikel Terkait</h3>
                </div>
            </div>
            <div class="row">
                @foreach($relatedArticles as $related)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="related-card">
                        @if($related->hasValidImage())
                            <img src="{{ $related->getImageUrl() }}" alt="{{ $related->title }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                <i class="fas fa-newspaper fa-2x"></i>
                            </div>
                        @endif
                        <div class="p-3">
                            <h6 class="mb-2">{{ Str::limit($related->title, 50) }}</h6>
                            <p class="text-muted small mb-3">{{ $related->created_at->format('d M Y') }}</p>
                            <a href="{{ route('frontend.information.show', $related->id) }}" class="btn btn-sm" style="background: #cab491; color: white;">Baca</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 footer-info">
                        <h3>CAD (Ceramic Art Dinoyo)</h3>
                        <p>Kampung Keramik Dinoyo adalah area di Kota Malang, Jawa Timur, yang terkenal dengan kerajinan keramik dan tembikar. Tempat ini menjadi pusat produksi keramik tradisional yang telah berkembang sejak lama.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
