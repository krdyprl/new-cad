<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.information') }} - Ceramic Art Dinoyo</title>
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
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            padding: 120px 0 80px 0; /* Extra top padding for fixed navbar */
            text-align: center;
        }
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }
        
        .news-section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .news-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .news-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .news-card-body {
            padding: 25px;
        }
        
        .news-card h5 {
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .news-card .excerpt {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #95a5a6;
            margin-bottom: 15px;
        }
        
        .btn-read-more {
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-read-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.4);
            color: white;
        }
        
        .no-articles {
            text-align: center;
            padding: 80px 0;
            color: #6c757d;
        }
        
        .no-articles i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #cab491;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <div class="page-header">
        <div class="container">
            <h1>Portal Berita Keramik Dinoyo</h1>
            <p>Ikuti perkembangan terkini seputar dunia keramik dan kegiatan di Kampung Keramik Dinoyo</p>
        </div>
    </div>

    <section class="news-section">
        <div class="container">
            @if(isset($articles) && $articles->count() > 0)
                <div class="row">                    @foreach($articles as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="news-card">
                            @if($article->hasValidImage())
                                <img src="{{ $article->getImageUrl() }}" alt="{{ $article->title }}" style="width: 100%; height: 250px; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 250px; background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                    <i class="fas fa-newspaper fa-3x"></i>
                                </div>
                            @endif
                            <div class="news-card-body">
                                <div class="news-meta">
                                    <span><i class="fas fa-calendar me-1"></i>{{ $article->created_at->format('d M Y') }}</span>
                                    <span><i class="fas fa-user me-1"></i>Admin</span>
                                </div>
                                <h5>{{ Str::limit($article->title, 60) }}</h5>
                                <p class="excerpt">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                                <a href="{{ route('frontend.information.show', $article->id) }}" class="btn btn-read-more">
                                    Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="no-articles">
                    <i class="fas fa-newspaper"></i>
                    <h3>Belum Ada Artikel</h3>
                    <p>Artikel dan berita terkait keramik Dinoyo akan segera hadir. Silakan kembali lagi nanti.</p>
                </div>
            @endif
        </div>
    </section>
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
