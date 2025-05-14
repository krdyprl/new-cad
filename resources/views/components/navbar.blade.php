<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <!-- Logo Only - Bigger Size -->
        <a class="navbar-brand" href="{{ route('frontend.home') }}">
            <img src="{{ asset('img/Cadputih.png') }}" alt="CAD Logo" class="navbar-logo">
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Content -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Main Navigation Links -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('home') || Request::routeIs('frontend.home') ? 'active' : '' }}" 
                       href="{{ route('frontend.home') }}">
                       HOME
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('frontend.information') ? 'active' : '' }}" 
                       href="{{ route('frontend.information') }}">
                       INFORMATION
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('frontend.catalog') ? 'active' : '' }}" 
                       href="{{ route('frontend.catalog') }}">
                       CATALOG
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('frontend.booking') ? 'active' : '' }}" 
                       href="{{ route('frontend.booking') }}">
                       BOOKING
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://ths.li/I0zPHco" target="_blank">
                       VIRTUAL TOUR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('frontend.contact') ? 'active' : '' }}" 
                       href="{{ route('frontend.contact') }}">
                       CHATBOT
                    </a>
                </li>
            </ul>
            
            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <!-- My Bookings Link -->
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('booking.my-bookings') ? 'active' : '' }}" 
                           href="{{ route('booking.my-bookings') }}">
                            <i class="fas fa-history me-1"></i>MY BOOKINGS
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>{{ Str::limit(Auth::user()->name, 15) }}
                        </a>                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(Auth::user()->is_admin)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-crown me-2"></i>Admin Panel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i>Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Login/Register Links -->
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('login') ? 'active' : '' }}" 
                           href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>LOGIN
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('register') ? 'active' : '' }}" 
                           href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>REGISTER
                        </a>
                    </li>
                @endauth
                
                <!-- Language Switcher -->
                @if(Route::has('language.switch'))
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="languageDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe me-1"></i>{{ session('locale', 'en') == 'id' ? 'ID' : 'EN' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('language.switch', ['locale' => 'en']) }}">
                                🇺🇸 English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('language.switch', ['locale' => 'id']) }}">
                                🇮🇩 Indonesia
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<style>
/* Navbar Styles - Fixed Position */
.navbar {
    background: rgba(20, 20, 20, 0.95) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
    padding: 0.75rem 0;
    transition: all 0.3s ease;
    z-index: 1050;
    width: 100%;
}

.navbar-brand {
    padding: 0.5rem 0;
}

.navbar-logo {
    height: 55px; /* Bigger logo size like welcome page */
    width: auto;
    transition: all 0.3s ease;
}

.navbar-logo:hover {
    transform: scale(1.05);
}

.nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
    font-family: "Montserrat", sans-serif;
    font-weight: 500;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    margin: 0 0.5rem;
    padding: 0.75rem 1rem !important;
    text-transform: uppercase;
    transition: all 0.3s ease;
    border-radius: 4px;
}
    border-radius: 4px;
    margin: 0 0.15rem;
}

.nav-link:hover {
    color: var(--primary-color) !important;
    background: rgba(202, 180, 145, 0.1);
    transform: translateY(-1px);
}

.nav-link.active {
    color: var(--primary-color) !important;
    background: rgba(202, 180, 145, 0.2);
}

.dropdown-menu {
    background: rgba(20, 20, 20, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.dropdown-item {
    color: rgba(255, 255, 255, 0.8) !important;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: rgba(202, 180, 145, 0.2);
    color: var(--primary-color) !important;
}

.btn-outline-light {
    border-color: rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
}

.btn-outline-light:hover {
    background: rgba(202, 180, 145, 0.2);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.navbar-toggler {
    border: 2px solid var(--primary-color);
    border-radius: 8px;
    padding: 8px 12px;
}

.navbar-toggler:hover {
    background: rgba(202, 180, 145, 0.2);
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Mobile Responsive */
@media (max-width: 991px) {
    .navbar-collapse {
        background: rgba(20, 20, 20, 0.98);
        border-radius: 8px;
        margin-top: 1rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .nav-link {
        text-align: center;
        margin: 0.3rem 0;
        padding: 0.8rem 1rem !important;
    }
      .navbar-logo {
        height: 55px;
    }
}

@media (max-width: 576px) {    .navbar-logo {
        height: 50px;
    }
    
    .nav-link {
        font-size: 0.9rem;
        padding: 0.7rem 0.8rem !important;
    }
}

/* Remove top padding from body globally */
body {
    padding-top: 0 !important;
    margin-top: 85px; /* Adjusted for bigger navbar */
}
</style>
