<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.booking') }} - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Master Stylesheet for consistent colors -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    
    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <!-- Alternative jsPDF if main CDN fails -->
    <script>
        // Backup jsPDF loading
        if (typeof window.jspdf === 'undefined') {
            document.write('<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"><\/script>');
        }
    </script>      <style>
        /* Booking Page Specific Styles using Global Variables */
        
        /* Body styling - remove top padding */
        body {
            margin: 0;
            padding: 0;
        }
          /* Page header with top margin to account for fixed navbar */
        .page-header {
            background: linear-gradient(135deg, #cab491 0%, #a68b57 100%);
            color: white;
            padding: 140px 0 80px 0; /* Increased top padding for bigger navbar */
            text-align: center;
            margin-top: 0;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        
        /* Booking section */
        .section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        /* Form Container */
        .booking-form {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xxl);
            box-shadow: var(--shadow-medium);
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid var(--border-dark);
            transition: var(--transition-normal);
        }
        
        .booking-form:hover {
            box-shadow: var(--shadow-heavy);
        }
        
        .booking-form h3 {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1.75rem;
            margin-bottom: var(--spacing-xl);
            text-align: center;
            position: relative;
            padding-bottom: var(--spacing-md);
        }
        
        .booking-form h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 60px;
            height: 3px;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
            transform: translateX(-50%);
            border-radius: var(--radius-sm);
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid var(--border-dark);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: var(--transition-normal);
            background: var(--bg-card);
            color: var(--text-dark);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(202, 180, 145, 0.1);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: var(--spacing-sm);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        /* Package Description */
        .package-info, #packageDescription {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid var(--border-dark);
            border-left: 4px solid var(--primary-color);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            margin-top: var(--spacing-md);
            transition: var(--transition-normal);
        }
        
        .package-info.show, #packageDescription {
            animation: slideIn 0.3s ease-out;
        }
        
        .package-info h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }
        
        /* Price Display */
        .price-display, #totalPriceSection .alert {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--text-light);
            padding: var(--spacing-lg);
            border-radius: var(--radius-md);
            text-align: center;
            margin: var(--spacing-lg) 0;
            border: none;
            box-shadow: var(--shadow-primary);
        }
        
        .price-display h4, #totalPrice {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        #priceBreakdown {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Submit Button */
        .btn-submit {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
            border: none;
            color: var(--text-light);
            padding: 15px 40px;
            border-radius: var(--radius-xl);
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition-normal);
        }
        
        .btn-submit:hover::before {
            left: 100%;
        }
        
        .btn-submit:hover {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary-color));
            color: var(--text-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(202, 180, 145, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Participant Counter */
        .participant-counter {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            justify-content: center;
        }
        
        .counter-btn {
            width: 40px;
            height: 40px;
            border: 2px solid var(--primary-color);
            border-radius: var(--radius-round);
            background: var(--bg-card);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-normal);
            font-weight: 600;
        }
        
        .counter-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
            transform: scale(1.1);
        }
        
        .counter-display {
            font-size: 1.2rem;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
            color: var(--text-dark);
        }
        
        /* Loading States */
        .loading-spinner {
            display: none;
            margin-left: var(--spacing-sm);
            animation: spin 1s linear infinite;
        }
        
        /* Alerts Custom */
        .alert {
            border-radius: var(--radius-md);
            border: none;
            padding: var(--spacing-md) var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        /* Special States */
        .form-control:disabled {
            background-color: #e9ecef;
            opacity: 0.8;
            cursor: not-allowed;
        }
        
        input[readonly] {
            background-color: #f8f9fa;
        }
        
        /* Debug/Test Buttons */
        .btn-outline-secondary,
        .btn-outline-info,
        .btn-outline-success {
            border-radius: var(--radius-md);
            transition: var(--transition-normal);
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 991px) {
            .booking-form {
                padding: var(--spacing-xl);
                margin: var(--spacing-md);
            }
            
            .booking-form h3 {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 767px) {
            .booking-form {
                padding: var(--spacing-lg);
            }
            
            .btn-submit {
                padding: 12px 30px;
                font-size: 1rem;
            }
            
            .participant-counter {
                flex-direction: column;
                gap: var(--spacing-sm);
            }
            
            .counter-btn {
                width: 35px;
                height: 35px;
            }
        }
        
        /* Time Input Styling */
        .time-input-field {
            width: 50px;
            padding: 8px;
            border: none;
            text-align: center;
            font-weight: 600;
            background: var(--bg-card);
        }
        
        .time-separator {
            padding: 8px 5px;
            background: #f8f9fa;
            border-left: 1px solid var(--border-dark);
            border-right: 1px solid var(--border-dark);
        }
    </style>
</head>
<body>
    @include('layouts.navbar')

    <style>
      /* Booking header with ceramic-vases banner on the right, cream fade on the left */
      .booking-header {
        background:
          linear-gradient(90deg, #F3EBDD 0%, #F3EBDD 45%, rgba(243,235,221,.55) 64%, rgba(243,235,221,0) 82%),
          url('{{ asset('img/banner_booking.png') }}') right center / cover no-repeat,
          #F3EBDD !important;
        padding: clamp(2.2rem,5vw,3.6rem) 0 !important;
      }
      .booking-header-inner { text-align: center; }
      .bh-title { display: inline-flex; align-items: center; gap: 1rem; }
      .booking-header-icon { width: 60px; height: 60px; border-radius: 50%; background: var(--glaze); color: #fff; display: grid; place-items: center; font-size: 1.5rem; flex: none; }
      .booking-header h1 { margin: 0; }
      .booking-header p { margin: .6rem auto 0; max-width: 48ch; }
      /* Neutral info box (was a yellow alert) */
      .booking-note { display: flex; gap: .8rem; align-items: flex-start; background: var(--porcelain); border: 1px solid var(--line); border-radius: 14px; padding: 1rem 1.1rem; margin-bottom: 1.6rem; color: var(--kiln-ink-soft); font-size: .92rem; }
      .booking-note .bn-icon { width: 30px; height: 30px; border-radius: 50%; background: rgba(46,110,97,.12); color: var(--glaze); display: grid; place-items: center; flex: none; font-size: .8rem; }
      .btn-submit { width: 100%; justify-content: center; }
      /* Docked "Pemandu CAD" chat sidebar — reuses the chatbot via embed */
      .booking-chat { background: #fff; border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow); overflow: hidden; height: 640px; position: sticky; top: 96px; }
      .booking-chat iframe { width: 100%; height: 100%; border: 0; display: block; }
      @media (max-width: 991px) { .booking-chat { height: 520px; position: static; margin-top: 1.5rem; } }
    </style>

    <!-- Page Header -->
    <div class="page-header booking-header">
        <div class="container">
            <div class="booking-header-inner">
                <div class="bh-title">
                    <span class="booking-header-icon"><i class="fas fa-calendar-check"></i></span>
                    <h1>{{ __('messages.booking') }}</h1>
                </div>
                <p>Pesan kunjungan Anda ke Kampung Keramik Dinoyo dan nikmati pengalaman membuat keramik.</p>
            </div>
        </div>
    </div>

    <!-- Booking Section -->
    <section class="section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="booking-form fade-in">
                        <h3 class="text-center mb-4">Form Pemesanan Kunjungan</h3>
                          @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if(session('info'))
                            <div class="alert alert-info">
                                {{ session('info') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        @guest
                            <div class="booking-note">
                                <span class="bn-icon"><i class="fas fa-info"></i></span>
                                <span><strong>Informasi:</strong> Anda dapat mengisi form booking tanpa login. Namun, saat submit pemesanan, Anda akan diminta untuk login atau mendaftar terlebih dahulu untuk melanjutkan proses pemesanan.</span>
                            </div>
                        @endguest
                          <form id="bookingForm" action="{{ route('frontend.booking.submit') }}" method="POST">
                            @csrf
                            <!-- Service Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih Paket Workshop:</label>
                                <select name="package" class="form-control" required onchange="updatePrice()">
                                    <option value="">-- Pilih Paket Workshop --</option>
                                    <option value="basic" data-price="50000">Paket Basic - Rp 50.000/orang</option>
                                    <option value="premium" data-price="85000">Paket Premium - Rp 85.000/orang</option>
                                    <option value="family" data-price="200000">Paket Family (4 orang) - Rp 200.000</option>
                                    <option value="group" data-price="400000">Paket Group (10 orang) - Rp 400.000</option>
                                </select>
                                
                                <!-- Package Description -->
                                <div class="mt-3 p-3 bg-light rounded" id="packageDescription" style="display: none;">
                                    <div id="packageDetails"></div>
                                </div>
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="fullName" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Peserta</label>
                                    <input type="number" name="participants" class="form-control" min="1" max="50" required onchange="updatePrice()" placeholder="Masukkan jumlah peserta">
                                    <small class="text-muted">Minimum 1 orang, maksimum 50 orang</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Kunjungan</label>
                                    <input type="date" name="visitDate" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waktu Kunjungan</label>
                                    <select name="visitTime" class="form-control" required>
                                        <option value="">Pilih Waktu</option>
                                        <option value="09:00">09:00 WIB</option>
                                        <option value="10:00">10:00 WIB</option>
                                        <option value="11:00">11:00 WIB</option>
                                        <option value="13:00">13:00 WIB</option>
                                        <option value="14:00">14:00 WIB</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Catatan Khusus</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Tuliskan permintaan khusus atau catatan untuk kunjungan Anda..."></textarea>
                            </div>
                            
                            <!-- Total Price Display -->
                            <div class="mb-4" id="totalPriceSection" style="display: none;">
                                <div class="alert alert-info">
                                    <h5 class="mb-2"><i class="fas fa-calculator me-2"></i>Estimasi Total Biaya:</h5>
                                    <div class="d-flex justify-content-between">
                                        <span id="priceBreakdown"></span>
                                        <strong class="fs-4 text-primary" id="totalPrice">Rp 0</strong>
                                    </div>
                                    <small class="text-muted">*Harga belum termasuk pajak dan biaya layanan</small>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pemesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="booking-chat">
                        <iframe src="{{ route('frontend.chatbot') }}?embed=1" title="Pemandu CAD" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <!-- Main Javascript File -->
    <script src="{{ asset('js/main.js') }}"></script>

    <script>        // Package details data
        const packageDetails = {
            workshop: {
                name: 'Workshop Keramik',
                price: 50000,
                duration: '2-3 jam',
                includes: [
                    'Pembuatan 1 keramik sederhana',
                    'Materi pembelajaran dasar',
                    'Alat-alat pembuatan',
                    'Sertifikat workshop'
                ]
            },
            tour: {
                name: 'Tur Keliling Kampung',
                price: 25000,
                duration: '1-2 jam',
                includes: [
                    'Tur berkeliling kampung keramik',
                    'Penjelasan sejarah keramik',
                    'Melihat proses pembuatan',
                    'Dokumentasi foto'
                ]
            },
            both: {
                name: 'Paket Lengkap (Workshop + Tur)',
                price: 70000,
                duration: '3-4 jam',
                includes: [
                    'Workshop pembuatan keramik',
                    'Tur keliling kampung',
                    'Materi pembelajaran lengkap',
                    'Alat-alat pembuatan',
                    'Sertifikat workshop',
                    'Dokumentasi foto'
                ]
            },
            family: {
                name: 'Paket Keluarga (4 orang)',
                price: 200000,
                duration: '3-4 jam',
                includes: [
                    'Workshop untuk 4 orang',
                    'Tur keliling kampung',
                    'Dokumentasi foto keluarga',
                    'Materi pembelajaran',
                    'Alat-alat lengkap',
                    'Snack dan minuman',
                    'Souvenir keluarga'
                ]
            },
            group: {
                name: 'Paket Grup (10 orang)',
                price: 400000,
                duration: '4-5 jam',
                includes: [
                    'Workshop untuk 10 orang',
                    'Team building activities',
                    'Tur keliling kampung',
                    'Dokumentasi lengkap',
                    'Materi pembelajaran advanced',
                    'Alat-alat professional',
                    'Makan siang ringan',
                    'Souvenir untuk semua peserta'
                ]
            }
        };// Admin WhatsApp number
        const adminWhatsApp = '62895397307475'; // Tanpa tanda + untuk URL        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            // Debug form elements
            debugFormElements();
            testJSPDF();
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            const visitDateInput = document.querySelector('input[name="visit_date"]');
            if (visitDateInput) {
                visitDateInput.min = today;
                console.log('✅ Date minimum set to:', today);
            }

            // Package selection change event
            const serviceSelect = document.querySelector('select[name="service"]');
            if (serviceSelect) {
                serviceSelect.addEventListener('change', updatePrice);
                console.log('✅ Package select event listener added');
            }

            // Participants change event
            const participantsInput = document.querySelector('input[name="participants"]');
            if (participantsInput) {
                participantsInput.addEventListener('input', updatePrice);
                console.log('✅ Participants input event listener added');
            }

            // Form submission
            const bookingForm = document.querySelector('form');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('📋 Form submitted, processing...');
                    submitBooking();
                });
                console.log('✅ Form submit event listener added');
            }
            
            console.log('🚀 Form initialization complete!');
        });
        
        // Additional check for jsPDF loading
        window.addEventListener('load', function() {
            if (typeof window.jspdf === 'undefined') {
                console.error('jsPDF failed to load. Trying alternative CDN...');
                // Try loading from alternative CDN
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js';
                script.onload = function() {
                    console.log('jsPDF loaded from alternative CDN');
                };
                script.onerror = function() {
                    console.error('Failed to load jsPDF from alternative CDN');
                };
                document.head.appendChild(script);
            }
        });

        // Update price when package or participants change
        function updatePrice() {
            const packageSelect = document.querySelector('select[name="service"]');
            const participantsInput = document.querySelector('input[name="participants"]');
            const packageDescription = document.getElementById('packageDescription');
            const packageDetails = document.getElementById('packageDetails');
            const totalPriceSection = document.getElementById('totalPriceSection');
            const priceBreakdown = document.getElementById('priceBreakdown');
            const totalPrice = document.getElementById('totalPrice');
            
            if (!packageSelect || !participantsInput) return;
            
            const selectedPackage = packageSelect.value;
            const participants = parseInt(participantsInput.value) || 1;
            
            if (selectedPackage && window.packageDetails && window.packageDetails[selectedPackage]) {
                const package = window.packageDetails[selectedPackage];
                
                // Show package description
                if (packageDetails) {
                    packageDetails.innerHTML = `
                        <h5>${package.name}</h5>
                        <p><strong>Durasi:</strong> ${package.duration}</p>
                        <p><strong>Harga:</strong> Rp ${package.price.toLocaleString('id-ID')}</p>
                        <p><strong>Termasuk:</strong></p>
                        <ul>
                            ${package.includes.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    `;
                }
                
                if (packageDescription) {
                    packageDescription.style.display = 'block';
                }
                
                // Calculate total price
                let total = 0;
                let breakdown = '';
                
                if (selectedPackage === 'family' || selectedPackage === 'group') {
                    // Fixed price packages
                    total = package.price;
                    breakdown = `${package.name} (Paket Fixed)`;
                } else {
                    // Per person packages
                    total = package.price * participants;
                    breakdown = `${package.name} x ${participants} orang`;
                }
                
                // Show price breakdown
                if (priceBreakdown) {
                    priceBreakdown.textContent = breakdown;
                }
                
                if (totalPrice) {
                    totalPrice.textContent = `Rp ${total.toLocaleString('id-ID')}`;
                }
                
                if (totalPriceSection) {
                    totalPriceSection.style.display = 'block';
                }
                
            } else {
                // Hide all price sections if no package selected
                if (packageDescription) {
                    packageDescription.style.display = 'none';
                }
                if (totalPriceSection) {
                    totalPriceSection.style.display = 'none';
                }
            }
        }
        
        // Make packageDetails global for updatePrice function
        window.packageDetails = {
            workshop: {
                name: 'Workshop Keramik',
                price: 50000,
                duration: '2-3 jam',
                includes: [
                    'Pembuatan 1 keramik sederhana',
                    'Materi pembelajaran dasar',
                    'Alat-alat pembuatan',
                    'Sertifikat workshop'
                ]
            },
            tour: {
                name: 'Tur Keliling Kampung',
                price: 25000,
                duration: '1-2 jam',
                includes: [
                    'Tur berkeliling kampung keramik',
                    'Penjelasan sejarah keramik',
                    'Melihat proses pembuatan',
                    'Dokumentasi foto'
                ]
            },
            both: {
                name: 'Paket Lengkap (Workshop + Tur)',
                price: 70000,
                duration: '3-4 jam',
                includes: [
                    'Workshop pembuatan keramik',
                    'Tur keliling kampung',
                    'Materi pembelajaran lengkap',
                    'Alat-alat pembuatan',
                    'Sertifikat workshop',
                    'Dokumentasi foto'
                ]
            },
            family: {
                name: 'Paket Keluarga (4 orang)',
                price: 200000,
                duration: '3-4 jam',
                includes: [
                    'Workshop untuk 4 orang',
                    'Tur keliling kampung',
                    'Dokumentasi foto keluarga',
                    'Materi pembelajaran',
                    'Alat-alat lengkap',
                    'Snack dan minuman',
                    'Souvenir keluarga'
                ]
            },
            group: {
                name: 'Paket Grup (10 orang)',
                price: 400000,
                duration: '4-5 jam',
                includes: [
                    'Workshop untuk 10 orang',
                    'Team building activities',
                    'Tur keliling kampung',
                    'Dokumentasi lengkap',
                    'Materi pembelajaran advanced',
                    'Alat-alat professional',
                    'Makan siang ringan',
                    'Souvenir untuk semua peserta'
                ]
            }
        };        // Submit booking via Laravel backend
        async function submitBooking() {
            const submitBtn = document.querySelector('button[type="submit"]');
            
            // Show loading
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            }
            
            try {
                // Get form data
                const formData = new FormData();                // Get form fields
                const packageSelect = document.querySelector('select[name="package"]');
                const nameInput = document.querySelector('input[name="fullName"]');
                const emailInput = document.querySelector('input[name="email"]');
                const phoneInput = document.querySelector('input[name="phone"]');
                const visitDateInput = document.querySelector('input[name="visitDate"]');
                const visitTimeSelect = document.querySelector('select[name="visitTime"]');
                const notesTextarea = document.querySelector('textarea[name="notes"]');
                const participantsInput = document.querySelector('input[name="participants"]');
                
                // Extract values
                const packageValue = packageSelect ? packageSelect.value : '';
                const fullName = nameInput ? nameInput.value.trim() : '';
                const email = emailInput ? emailInput.value.trim() : '';
                const phone = phoneInput ? phoneInput.value.trim() : '';
                const visitDate = visitDateInput ? visitDateInput.value : '';
                const visitTime = visitTimeSelect ? visitTimeSelect.value : '';
                const notes = notesTextarea ? notesTextarea.value.trim() : '';
                const participants = participantsInput ? parseInt(participantsInput.value) || 1 : 1;
                
                console.log('Form data extracted:', {
                    packageValue, fullName, email, phone, visitDate, visitTime, participants
                });
                
                // Validate required fields
                if (!packageValue || !fullName || !email || !phone || !visitDate || !visitTime) {
                    alert('Mohon lengkapi semua field yang wajib diisi!');
                    return;
                }                // Prepare form data for Laravel
                formData.append('package', packageValue);
                formData.append('fullName', fullName);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('participants', participants);
                formData.append('visitDate', visitDate);
                formData.append('visitTime', visitTime);
                formData.append('notes', notes);
                formData.append('_token', '{{ csrf_token() }}');
                
                console.log('Sending data to backend...');
                
                // Send to Laravel backend
                const response = await fetch('{{ route("frontend.booking.submit") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    alert('🎉 Pemesanan berhasil!\n\nInvoice PDF telah digenerate dan dikirim ke admin WhatsApp.\nAnda akan diarahkan ke WhatsApp admin...');
                    
                    // Delay then redirect to WhatsApp
                    setTimeout(() => {
                        window.open(result.whatsapp_url, '_blank');
                        
                        // Reset form after redirect
                        setTimeout(() => {
                            resetBookingForm();
                        }, 1000);
                    }, 2000);
                    
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        let errorMessages = [];
                        for (let field in result.errors) {
                            errorMessages.push(`• ${result.errors[field][0]}`);
                        }
                        alert('❌ Error Validasi:\n\n' + errorMessages.join('\n'));
                    } else {
                        alert('❌ Error: ' + (result.message || 'Terjadi kesalahan saat memproses pemesanan.'));
                    }                }
                
            } catch (error) {
                console.error('Booking submission error:', error);
                alert('❌ Terjadi kesalahan koneksi. Silakan coba lagi.\n\nDetail error: ' + error.message);
            } finally {
                // Hide loading
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Pemesanan';
                }
            }
        }
        
        // Reset booking form
        function resetBookingForm() {
            const form = document.getElementById('bookingForm');
            if (form) {
                form.reset();
                // Reset package info display
                const packageInfo = document.getElementById('packageInfo');
                if (packageInfo) packageInfo.classList.remove('show');
            }
        }
        
        // Reset booking form
        function resetBookingForm() {
            const bookingForm = document.querySelector('form');
            if (bookingForm) {
                bookingForm.reset();
            }
            
            // Reset participant count
            const participantCount = document.getElementById('participantCount');
            const participantInput = document.getElementById('participantInput');
            if (participantCount) participantCount.textContent = '1';
            if (participantInput) participantInput.value = '1';
            
            // Hide sections
            const packageDescription = document.getElementById('packageDescription');
            const totalPriceSection = document.getElementById('totalPriceSection');
            const priceDisplay = document.getElementById('priceDisplay');
            const packageInfo = document.getElementById('packageInfo');
            
            if (packageDescription) packageDescription.style.display = 'none';
            if (totalPriceSection) totalPriceSection.style.display = 'none';
            if (priceDisplay) priceDisplay.style.display = 'none';
            if (packageInfo) packageInfo.classList.remove('show');
        }
        // Generate PDF invoice
        function generatePDF(data) {
            try {
                console.log('Starting PDF generation...', data); // Debug log
                
                // Check if jsPDF is loaded
                if (typeof window.jspdf === 'undefined') {
                    throw new Error('jsPDF library tidak dapat dimuat. Mohon refresh halaman dan coba lagi.');
                }
                
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Colors
                const primaryColor = [202, 180, 145]; // #cab491
                const darkColor = [51, 51, 51]; // #333333
                
                // Header background
                doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
                doc.rect(0, 0, 210, 40, 'F');
                
                // Logo/Title
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(24);
                doc.setFont('helvetica', 'bold');
                doc.text('CAD', 20, 25);
                
                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                doc.text('Ceramic Art Dinoyo', 20, 32);
                
                // Invoice title
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(18);
                doc.setFont('helvetica', 'bold');
                doc.text('INVOICE PEMESANAN', 120, 25);
                
                // Invoice number and date
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                const invoiceNo = `INV-${new Date().getTime()}`;
                const currentDate = new Date().toLocaleDateString('id-ID');
                doc.text(`No: ${invoiceNo}`, 120, 32);
                doc.text(`Tanggal: ${currentDate}`, 120, 37);
                
                // Customer info section
                doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('DETAIL CUSTOMER', 20, 55);
                
                doc.setFontSize(11);
                doc.setFont('helvetica', 'normal');
                let yPos = 65;
                doc.text(`Nama: ${data.fullName}`, 20, yPos);
                doc.text(`Email: ${data.email}`, 20, yPos + 7);
                doc.text(`Telepon: ${data.phone}`, 20, yPos + 14);
                
                // Booking details section
                yPos = 95;
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('DETAIL PEMESANAN', 20, yPos);
                
                doc.setFontSize(11);
                doc.setFont('helvetica', 'normal');
                yPos += 10;
                doc.text(`Paket: ${data.packageName}`, 20, yPos);
                doc.text(`Jumlah Peserta: ${data.participants} orang`, 20, yPos + 7);
                doc.text(`Tanggal Kunjungan: ${data.visitDate}`, 20, yPos + 14);
                doc.text(`Waktu: ${data.visitTime}`, 20, yPos + 21);
                
                if (data.notes && data.notes.trim()) {
                    doc.text(`Catatan: ${data.notes}`, 20, yPos + 28);
                    yPos += 7;
                }
                
                // Price breakdown section
                yPos = 150;
                doc.setFillColor(248, 249, 250);
                doc.rect(20, yPos, 170, 30, 'F');
                
                doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                doc.text('Rincian Harga:', 25, yPos + 10);
                
                if (data.packageType === 'family' || data.packageType === 'group') {
                    doc.text(`${data.packageName} (Paket Fixed)`, 25, yPos + 17);
                    doc.text(`Rp ${data.packagePrice.toLocaleString('id-ID')}`, 150, yPos + 17);
                } else {
                    doc.text(`${data.packageName} x ${data.participants} orang`, 25, yPos + 17);
                    doc.text(`Rp ${data.packagePrice.toLocaleString('id-ID')} x ${data.participants}`, 120, yPos + 17);
                }
                
                // Total section
                doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
                doc.rect(20, yPos + 30, 170, 15, 'F');
                
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.text('TOTAL PEMBAYARAN:', 25, yPos + 41);
                doc.text(`Rp ${data.totalPrice.toLocaleString('id-ID')}`, 140, yPos + 41);
                
                // Footer section
                yPos = 220;
                doc.setTextColor(darkColor[0], darkColor[1], darkColor[2]);
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('Kampung Keramik Dinoyo', 20, yPos);
                doc.text('Jl. Mt Haryono 9 No.336, Dinoyo, Lowokwaru, Malang', 20, yPos + 5);
                doc.text('Telepon: 0812-3553-1979', 20, yPos + 10);
                doc.text('Instagram: @keramikdinoyo', 20, yPos + 15);
                
                doc.setFont('helvetica', 'italic');
                doc.text('Terima kasih atas kepercayaan Anda!', 20, yPos + 25);
                
                // Generate filename
                const filename = `Invoice_CAD_${data.fullName.replace(/\s+/g, '_')}_${new Date().getTime()}.pdf`;
                
                console.log('PDF generated successfully, downloading...'); // Debug log
                
                // Download the PDF
                doc.save(filename);
                
                console.log('PDF download triggered'); // Debug log
                
                return true;
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Gagal membuat PDF: ' + error.message);
                return false;
            }
        }
        



    </script>
</body>
</html>
