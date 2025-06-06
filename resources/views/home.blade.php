<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>Ceramic Art Dinoyo</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicons -->
  <link href="{{ asset('img/Cadputih.png') }}" rel="icon">
  <link href="{{ asset('img/Cadputih.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Montserrat:300,400,500,700" rel="stylesheet">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
  
  <!-- Master CSS - Controls all colors consistently -->
  <link href="{{ asset('css/master.css') }}" rel="stylesheet">
  
  <!-- Home hero + sections (design tokens live in ceramic.css) -->
  <style>
    /* Split hero — text left, rotating photo right */
    #intro { position: relative; background: linear-gradient(180deg,#F7F2E9,#F1E8DA); padding: clamp(1.5rem,4vw,3.5rem) 0 clamp(2.5rem,5vw,4rem); overflow: hidden; }
    .hero-row { align-items: center; }
    .hero-copy { padding: 1rem 0; }
    .hero-eyebrow {
      display: inline-flex; align-items: center; gap: .6rem;
      text-transform: uppercase; letter-spacing: .24em; font-size: .72rem; font-weight: 600;
      color: var(--clay); margin-bottom: 1.2rem;
    }
    .hero-title {
      font-family: "Fraunces", Georgia, serif; font-weight: 600;
      font-size: clamp(2.5rem, 5vw, 4.2rem); line-height: 1.04; letter-spacing: -.01em;
      color: var(--kiln-ink); margin-bottom: 1.1rem;
    }
    .hero-title .text-glaze { color: var(--glaze); }
    .hero-sub { font-size: clamp(1rem,1.4vw,1.15rem); color: var(--kiln-ink-soft); max-width: 40ch; margin-bottom: 1.8rem; }
    .hero-figure { position: relative; }
    .hero-figure .carousel, .hero-figure .carousel-inner, .hero-figure .carousel-item { height: clamp(380px,44vw,540px); }
    .hero-figure .carousel { border-radius: 24px; overflow: hidden; box-shadow: var(--shadow); }
    .hero-figure .carousel-item img { width: 100%; height: 100%; object-fit: cover; }
    .carousel-indicators { position: static; margin: 1.6rem 0 0; display: flex; justify-content: center; gap: 9px; }
    .carousel-indicators button { width: 34px !important; height: 5px !important; border-radius: 4px; background: var(--clay) !important; opacity: .3; border: 0; transition: opacity .3s, width .3s; }
    .carousel-indicators .active { opacity: 1; width: 48px !important; background: var(--glaze) !important; }
    .carousel-control-prev, .carousel-control-next { display: none; }

    /* Hero feature points */
    .feature-points { margin-top: 2.2rem; padding-top: 1.6rem; border-top: 1px solid var(--line); }
    .feature-point { display: flex; gap: .75rem; }
    .feature-point .ring { margin-top: .25rem; }
    .feature-point h6 { font-weight: 700; font-size: .92rem; margin: 0 0 .15rem; color: var(--kiln-ink); }
    .feature-point p { font-size: .84rem; color: var(--kiln-ink-soft); margin: 0; line-height: 1.4; }

    /* Featured experience cards */
    #featured-services { background: var(--clay-cream); }
    #featured-services .box {
      background: var(--porcelain); border: 1px solid var(--line); border-radius: 18px;
      box-shadow: var(--shadow); padding: 2rem 1.6rem; height: 100%;
      display: flex; flex-direction: column; align-items: flex-start; text-align: left;
      transition: transform .25s ease, box-shadow .25s ease;
    }
    #featured-services .box:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(42,33,27,.5); }
    #featured-services .box > i {
      font-size: 1.55rem; color: #fff; margin-bottom: 1.2rem;
      width: 3.2rem; height: 3.2rem; display: grid; place-items: center;
      background: var(--glaze); border-radius: 50%;
    }
    #featured-services .box .title { font-family: "Fraunces", serif; font-size: 1.2rem; font-weight: 600; margin-bottom: .4rem; }
    #featured-services .box .title a { color: var(--kiln-ink); }
    #featured-services .box .title a:hover { color: var(--glaze); }
    #featured-services .box .description { color: var(--kiln-ink-soft); font-size: .92rem; margin: 0; }
    #featured-services .box .card-cta { margin-top: auto; padding-top: 1.2rem; color: var(--glaze); font-weight: 600; font-size: .9rem; display: inline-flex; align-items: center; gap: .45rem; }
    #featured-services .box .card-cta i { transition: transform .2s ease; }
    #featured-services .box:hover .card-cta i { transform: translateX(4px); }
    #featured-services .col-lg-3 { margin-bottom: 1.5rem; }

    /* Plan-your-visit band */
    .home-cta { padding: 0 0 clamp(3rem,6vw,5rem); background: var(--clay-cream); }
    .home-cta-band {
      display: grid; grid-template-columns: 280px 1fr auto; align-items: center; gap: 2rem;
      background: var(--porcelain); border: 1px solid var(--line); border-radius: 24px; box-shadow: var(--shadow); overflow: hidden;
    }
    .home-cta-band .band-img { align-self: stretch; min-height: 230px; background: url('{{ asset('img/about-plan.jpg') }}') center/cover; }
    .home-cta-band .band-body { padding: 2.2rem 0; }
    .band-eyebrow { display: block; text-transform: uppercase; letter-spacing: .22em; font-size: .72rem; font-weight: 600; color: var(--clay); margin-bottom: .7rem; }
    .band-title { font-family: "Fraunces", serif; font-weight: 600; font-size: clamp(1.5rem,2.6vw,2.1rem); color: var(--kiln-ink); margin-bottom: .6rem; line-height: 1.15; }
    .band-sub { color: var(--kiln-ink-soft); margin: 0; }
    .home-cta-band .band-action { padding-right: 2.2rem; }
    @media (max-width: 900px) {
      .home-cta-band { grid-template-columns: 1fr; }
      .home-cta-band .band-body { padding: 1.8rem; }
      .home-cta-band .band-action { padding: 0 1.8rem 2rem; }
      .home-cta-band .band-img { min-height: 200px; }
    }
    @media (max-width: 768px) {
      .hero-figure { margin-top: 2rem; }
      .feature-point { margin-bottom: 1.2rem; }
    }

    body { padding-top: 0 !important; margin-top: 76px !important; }
  </style>
</head>

<body>
  {{-- Updated navbar to match information page --}}
  @include('layouts.navbar')
  @php $id = app()->getLocale() === 'id'; @endphp
  <!--========================
    Intro Section
===========================-->
<section id="intro">
  <div class="container">
    <div class="row hero-row">
      <div class="col-lg-5 hero-copy">
        <span class="hero-eyebrow"><span class="ring"></span> {{ $id ? 'Dibentuk di atas roda putar' : "Shaped on the potter's wheel" }}</span>
        <h1 class="hero-title">{!! $id ? 'Dunia keramik, <span class="text-glaze">penuh warna</span>' : 'A world of ceramics, <span class="text-glaze">full of color</span>' !!}</h1>
        <p class="hero-sub">{{ $id ? 'Dari tanah liat menjadi karya seni — saksikan setiap proses, dari membentuk hingga membakar di tungku.' : 'From clay to art — watch every step, from shaping to firing in the kiln.' }}</p>
        <a href="#featured-services" class="btn-get-started scrollto">{{ $id ? 'Jelajahi Kampung' : 'Explore the Village' }} <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
      <div class="col-lg-7 hero-figure">
        <div id="introCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active"><img src="{{ asset('img/intro-carousel/1.jpg') }}" alt="Perajin keramik Dinoyo"></div>
            <div class="carousel-item"><img src="{{ asset('img/intro-carousel/2.jpg') }}" alt=""></div>
            <div class="carousel-item"><img src="{{ asset('img/intro-carousel/3.jpg') }}" alt=""></div>
            <div class="carousel-item"><img src="{{ asset('img/intro-carousel/4.jpg') }}" alt=""></div>
            <div class="carousel-item"><img src="{{ asset('img/intro-carousel/5.jpg') }}" alt=""></div>
          </div>
        </div>
      </div>
    </div>

    <ol class="carousel-indicators"></ol>

    <div class="feature-points row">
      <div class="col-md-4 feature-point reveal">
        <span class="ring"></span>
        <div>
          <h6>{{ $id ? 'Pengalaman Autentik' : 'Authentic Experience' }}</h6>
          <p>{{ $id ? 'Lihat perajin bekerja langsung.' : 'See real artisans at work.' }}</p>
        </div>
      </div>
      <div class="col-md-4 feature-point reveal reveal-2">
        <span class="ring"></span>
        <div>
          <h6>{{ $id ? 'Belajar & Menjelajah' : 'Learn & Explore' }}</h6>
          <p>{{ $id ? 'Temukan seni di balik setiap karya.' : 'Discover the art behind every piece.' }}</p>
        </div>
      </div>
      <div class="col-md-4 feature-point reveal reveal-3">
        <span class="ring"></span>
        <div>
          <h6>{{ $id ? 'Dukung Lokal' : 'Support Local' }}</h6>
          <p>{{ $id ? 'Memberdayakan kerajinan & komunitas lokal.' : 'Empowering local crafts and community.' }}</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- #intro -->
  <main id="main">
    <!--========================
      Featured Services Section
    ============================-->
    <section id="featured-services" class="section">
      <div class="container">
        <div class="row align-items-end mb-5">
          <div class="col-lg-8 reveal">
            <span class="section-eyebrow"><span class="ring"></span> {{ __('messages.home_eyebrow') }}</span>
            <h2 class="section-title">{{ __('messages.home_section_title_a') }} <span class="text-glaze">{{ __('messages.home_section_title_b') }}</span></h2>
          </div>
          <div class="col-lg-4 reveal reveal-2">
            <p class="section-lead ms-lg-auto">{{ __('messages.home_section_lead') }}</p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3 col-md-6 reveal">
            <div class="box">
              <i class="ion-ios-chatbubble-outline"></i>
              <h4 class="title"><a href="{{ url('/chatbot') }}">{{ __('messages.chatbot_feature') }}</a></h4>
              <p class="description">{{ __('messages.chatbot_desc') }}</p>
              <a href="{{ url('/chatbot') }}" class="card-cta">{{ $id ? 'Coba Chatbot' : 'Try the Chatbot' }} <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 reveal reveal-2">
            <div class="box">
              <i class="ion-easel"></i>
              <h4 class="title"><a href="https://ths.li/I0zPHco" target="_blank">{{ __('messages.virtual_tour') }}</a></h4>
              <p class="description">{{ __('messages.vtour_desc') }}</p>
              <a href="https://ths.li/I0zPHco" target="_blank" class="card-cta">{{ $id ? 'Mulai Tur' : 'Take the Tour' }} <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 reveal reveal-3">
            <div class="box">
              <i class="ion-bag"></i>
              <h4 class="title"><a href="{{ route('frontend.catalog') }}">{{ __('messages.catalog') }}</a></h4>
              <p class="description">{{ __('messages.catalog_desc') }}</p>
              <a href="{{ route('frontend.catalog') }}" class="card-cta">{{ $id ? 'Lihat Katalog' : 'Browse Catalog' }} <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 reveal reveal-4">
            <div class="box">
              <i class="ion-calendar"></i>
              <h4 class="title"><a href="{{ route('frontend.booking') }}">{{ __('messages.booking') }}</a></h4>
              <p class="description">{{ __('messages.booking_desc') }}</p>
              <a href="{{ route('frontend.booking') }}" class="card-cta">{{ $id ? 'Pesan Sekarang' : 'Book Now' }} <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--========================
      Plan-your-visit band
    ============================-->
    <section class="home-cta">
      <div class="container">
        <div class="home-cta-band reveal">
          <div class="band-img" role="img" aria-label="{{ $id ? 'Vas keramik Dinoyo' : 'Dinoyo ceramic vases' }}"></div>
          <div class="band-body">
            <span class="band-eyebrow">{{ $id ? 'Kunjungi · Belajar · Berkarya' : 'Visit · Learn · Create' }}</span>
            <h2 class="band-title">{{ $id ? 'Rasakan seni keramik di jantung Dinoyo.' : 'Experience the art of ceramics in the heart of Dinoyo.' }}</h2>
            <p class="band-sub">{{ $id ? 'Rencanakan kunjungan dan jadilah bagian dari komunitas kreatif kami.' : 'Plan your visit and be part of our creative community.' }}</p>
          </div>
          <div class="band-action">
            <a href="{{ route('frontend.booking') }}" class="btn-get-started">{{ $id ? 'Rencanakan Kunjungan' : 'Plan Your Visit' }} <i class="fas fa-arrow-right ms-2"></i></a>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--========================
    Footer
  ============================-->
    @include('layouts.footer')
  <a href="#" class="scrollToTop"><i class="ion-chevron-up"></i></a>
  
  <!-- JavaScript Libraries -->
  <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('lib/jquery/jquery-migrate.min.js') }}"></script>
  <!-- Bootstrap JS from CDN for consistency -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
  <script src="{{ asset('lib/superfish/hoverIntent.js') }}"></script>
  <script src="{{ asset('lib/superfish/superfish.min.js') }}"></script>
  <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
  <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
  <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
  <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>
  <script src="{{ asset('lib/touchSwipe/jquery.touchSwipe.min.js') }}"></script>
  
  <!-- Contact Form JavaScript File -->
  <script src="{{ asset('contactform/contactform.js') }}"></script>  <!-- Template Main Javascript File -->
  <script src="{{ asset('js/main.js') }}"></script>
  
  <!-- Bootstrap 5 Carousel Initialization -->
  <script>
    $(document).ready(function() {
      // Initialize carousel indicators for Bootstrap 5
      var totalItems = $('.carousel-item').length;
      var indicators = '';
      for (var i = 0; i < totalItems; i++) {
        var activeClass = i === 0 ? 'active' : '';
        indicators += '<button type="button" data-bs-target="#introCarousel" data-bs-slide-to="' + i + '" class="' + activeClass + '"></button>';
      }
      $('.carousel-indicators').html(indicators);
      
      // Initialize Bootstrap 5 carousel
      var carousel = new bootstrap.Carousel(document.getElementById('introCarousel'), {
        interval: 5000,
        ride: 'carousel'
      });
    });
  </script>
</body>
</html>
