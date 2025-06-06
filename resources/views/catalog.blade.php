<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Katalog Produk - Ceramic Art Dinoyo</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Master Stylesheet for consistent colors -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Ionicons -->
    <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    
    <!-- Master CSS -->
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .page-header {
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            padding: 120px 0 80px 0;
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }
        
        .catalog-section {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .filter-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .product-card-body {
            padding: 25px;
        }
        
        .product-card h5 {
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .product-category {
            display: inline-block;
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        
        .product-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .btn-view-product {
            background: linear-gradient(135deg, #cab491 0%, #8b7355 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-view-product:hover {
            color: white;
            transform: scale(1.05);
        }
        
        .no-products {
            text-align: center;
            padding: 60px 20px;
        }
        
        .no-products i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        /* Footer Styles */
        #footer {
            background: #343a40;
            color: #fff;
            padding: 40px 0 0 0;
            font-size: 14px;
        }
        
        #footer .footer-top {
            padding: 60px 0 30px 0;
        }
        
        #footer .footer-info h3 {
            font-size: 26px;
            margin: 0 0 20px 0;
            padding: 2px 0 2px 0;
            line-height: 1;
            color: #cab491;
            font-weight: 700;
        }
        
        #footer .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        #footer .footer-links ul li {
            padding: 8px 0;
            border-bottom: 1px solid #495057;
        }
        
        #footer .footer-links ul li:first-child {
            padding-top: 0;
        }
        
        #footer .footer-links ul a {
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
        }
        
        #footer .footer-links ul a:hover {
            color: #cab491;
        }
        
        #footer .footer-contact p {
            line-height: 26px;
        }
        
        #footer .footer-contact .social-links a {
            color: #fff;
            margin-right: 15px;
            font-size: 16px;
            transition: 0.3s;
        }
        
        #footer .footer-contact .social-links a:hover {
            color: #cab491;
        }
        
        #footer .copyright {
            text-align: center;
            padding: 30px 0;
            border-top: 1px solid #495057;
            margin-top: 30px;
        }
        
        /* Scroll to Top Button */
        .scrollToTop {
            width: 40px;
            height: 40px;
            position: fixed;
            bottom: 50px;
            right: 50px;
            background: #cab491;
            color: #fff;
            text-align: center;
            line-height: 40px;
            z-index: 9999;
            border-radius: 3px;
            transition: all 0.3s ease;
            display: none;
        }
        
        .scrollToTop:hover {
            background: #a68b57;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    @include('layouts.navbar')

    <div class="page-header">
        <div class="container">
            <h1>Katalog Produk</h1>
            <p>Koleksi keramik terbaik dari Dinoyo</p>
        </div>
    </div>

    <section class="catalog-section">
        <div class="container">
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('frontend.catalog') }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Kategori</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari Produk</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ $search }}" placeholder="Nama produk, deskripsi...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card" onclick="showProductModal({{ $product->id }})">
                            @if($product->hasValidImage())
                                <img src="{{ $product->getImageUrl() }}" alt="{{ $product->name }}">
                            @else
                                <div style="width: 100%; height: 250px; background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif
                            <div class="product-card-body">
                                <span class="product-category">{{ $product->category }}</span>
                                <h5>{{ $product->name }}</h5>
                                <div class="product-price">{{ $product->formatted_price }}</div>
                                <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                                <button type="button" class="btn-view-product">
                                    Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Tidak Ada Produk</h3>
                    <p class="text-muted">
                        @if($search || $category)
                            Tidak ditemukan produk yang sesuai dengan filter Anda.
                        @else
                            Belum ada produk yang tersedia saat ini.
                        @endif
                    </p>
                    @if($search || $category)
                        <a href="{{ route('frontend.catalog') }}" class="btn btn-primary">
                            Lihat Semua Produk
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!--========================
    Footer
  ============================-->
    @include('layouts.footer')

    <a href="#" class="scrollToTop"><i class="ion-chevron-up"></i></a>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productModalBody">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="scrollToTop"><i class="ion-chevron-up"></i></a>
    
    <!-- JavaScript Libraries -->
    <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/jquery/jquery-migrate.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const products = @json($products->items());
        
        function showProductModal(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            document.getElementById('productModalTitle').textContent = product.name;
            
            const modalBody = document.getElementById('productModalBody');
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        ${product.image ? 
                            `<img src="${product.image.startsWith('http') ? product.image : '{{ config("app.url") }}/' + product.image}" 
                                  alt="${product.name}" class="img-fluid rounded">` :
                            `<div style="width: 100%; height: 300px; background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); display: flex; align-items: center; justify-content: center; color: white; border-radius: 10px;">
                                <i class="fas fa-image fa-4x"></i>
                             </div>`
                        }
                    </div>
                    <div class="col-md-6">
                        <span class="badge" style="background: linear-gradient(135deg, #cab491 0%, #8b7355 100%); margin-bottom: 15px;">${product.category}</span>
                        <h4>${product.name}</h4>
                        <h5 class="text-danger mb-3">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</h5>
                        <p>${product.description}</p>
                        <h6>Spesifikasi:</h6>
                        <div class="specifications">
                            ${product.specifications.split('\n').map(spec => 
                                spec.trim() ? `<div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>${spec.trim()}</span>
                                </div>` : ''
                            ).join('')}
                        </div>
                        ${product.ecommerce_link ? 
                            `<a href="${product.ecommerce_link}" target="_blank" class="btn btn-success mt-3">
                                <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                            </a>` : 
                            `<button class="btn btn-secondary mt-3" disabled>
                                Link Belum Tersedia
                            </button>`
                        }
                    </div>
                </div>
            `;
            
            modal.show();
        }
    </script>
    
    <!-- Additional Libraries for Footer -->
     <!-- <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/superfish/hoverIntent.js') }}"></script>
    <script src="{{ asset('lib/superfish/superfish.min.js') }}"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('lib/touchSwipe/jquery.touchSwipe.min.js') }}"></script>
    <script src="{{ asset('contactform/contactform.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>-->
</body>
</html>
