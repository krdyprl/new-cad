<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Pemesanan Saya - Ceramic Art Dinoyo</title>
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
            padding: 140px 0 80px 0; /* Increased top padding for bigger navbar */
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        
        .bookings-section {
            padding: 60px 0;
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
        }
        
        .booking-header {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-id {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .booking-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #ffc107;
            color: #000;
        }
        
        .status-confirmed {
            background: #28a745;
            color: white;
        }
        
        .status-completed {
            background: #17a2b8;
            color: white;
        }
        
        .status-cancelled {
            background: #dc3545;
            color: white;
        }
        
        .booking-body {
            padding: 25px;
        }
        
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .detail-icon {
            background: #cab491;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .booking-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-download {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(202, 180, 145, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
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
            <h1><i class="fas fa-calendar-check"></i> Pemesanan Saya</h1>
            <p class="lead">Kelola dan lihat riwayat pemesanan workshop keramik Anda</p>
        </div>
    </div>

    <section class="bookings-section">
        <div class="container">
            @if($bookings->count() > 0)
                <div class="row">
                    <div class="col-12">
                        @foreach($bookings as $booking)
                        <div class="booking-card">
                            <div class="booking-header">
                                <div>
                                    <div class="booking-id">{{ $booking->booking_id }}</div>
                                    <small>{{ $booking->created_at->format('d F Y, H:i') }} WIB</small>
                                </div>
                                <div class="booking-status status-{{ $booking->status }}">
                                    @switch($booking->status)
                                        @case('pending')
                                            <i class="fas fa-clock me-1"></i>Menunggu
                                            @break
                                        @case('confirmed')
                                            <i class="fas fa-check me-1"></i>Dikonfirmasi
                                            @break
                                        @case('completed')
                                            <i class="fas fa-check-double me-1"></i>Selesai
                                            @break
                                        @case('cancelled')
                                            <i class="fas fa-times me-1"></i>Dibatalkan
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            
                            <div class="booking-body">
                                <div class="booking-details">
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $booking->package_name }}</strong><br>
                                            <small class="text-muted">{{ $booking->participants }} peserta</small>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $booking->visit_date->format('d F Y') }}</strong><br>
                                            <small class="text-muted">{{ $booking->visit_time }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-money-bill"></i>
                                        </div>
                                        <div>
                                            <strong>Rp {{ number_format($booking->total, 0, ',', '.') }}</strong><br>
                                            <small class="text-muted">Total pembayaran</small>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $booking->full_name }}</strong><br>
                                            <small class="text-muted">{{ $booking->phone }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($booking->notes)
                                <div class="mb-3">
                                    <strong>Catatan:</strong>
                                    <p class="text-muted mb-0">{{ $booking->notes }}</p>
                                </div>
                                @endif
                                
                                <div class="booking-actions">
                                    @if($booking->pdf_file)
                                    <a href="{{ route('frontend.booking.pdf', $booking->id) }}" class="btn-download" target="_blank">
                                        <i class="fas fa-download me-2"></i>Download Invoice PDF
                                    </a>
                                    @endif
                                    
                                    @if($booking->status === 'pending')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-info-circle me-1"></i>Menunggu konfirmasi admin
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Belum Ada Pemesanan</h3>
                    <p>Anda belum melakukan pemesanan workshop keramik. Mari mulai booking pengalaman menarik di Kampung Keramik Dinoyo!</p>
                    <a href="{{ route('frontend.booking') }}" class="btn-download">
                        <i class="fas fa-plus me-2"></i>Buat Pemesanan Baru
                    </a>
                </div>
            @endif
        </div>
    </section>

    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 footer-info">
                        <h3>CAD (Ceramic Art Dinoyo)</h3>
                        <p>Kampung Keramik Dinoyo adalah area di Kota Malang, Jawa Timur, yang terkenal dengan kerajinan keramik dan tembikar.</p>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>{{ __('messages.useful_links') }}</h4>
                        <ul>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.home') }}">Home</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.about') }}">About us</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.catalog') }}">Catalog</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.booking') }}">Booking</a></li>
                            <li><i class="fas fa-chevron-right me-2"></i> <a href="{{ route('frontend.contact') }}">Chatbot</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-5 footer-contact">
                        <h4>{{ __('messages.contact_info') }}</h4>
                        <p>
                            {{ __('messages.address') }}<br>
                            <strong>{{ __('messages.phone') }}:</strong> +62 341 123456<br>
                            <strong>{{ __('messages.email') }}:</strong> info@ceramicartdinoyo.com<br>
                        </p>
                    </div>

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
