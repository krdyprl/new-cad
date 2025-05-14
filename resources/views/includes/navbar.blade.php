<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('frontend.home') }}">
            <img src="{{ asset('img/Cadputih.png') }}" alt="CAD Logo" style="height: 40px;">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" 
                       href="{{ route('frontend.home') }}">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}" 
                       href="{{ route('frontend.about') }}">
                        <i class="fas fa-info-circle me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.catalog') ? 'active' : '' }}" 
                       href="{{ route('frontend.catalog') }}">
                        <i class="fas fa-th-large me-1"></i>Catalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.booking') ? 'active' : '' }}" 
                       href="{{ route('frontend.booking') }}">
                        <i class="fas fa-calendar-alt me-1"></i>Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" 
                       href="{{ route('frontend.contact') }}">
                        <i class="fas fa-robot me-1"></i>Chatbot
                    </a>
                </li>
            </ul>
            
            <!-- User Authentication Menu -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <!-- My Bookings Link -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.my-bookings') ? 'active' : '' }}" 
                           href="{{ route('booking.my-bookings') }}">
                            <i class="fas fa-history me-1"></i>My Bookings
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" 
                           href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                           href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </li>
                @endauth
            </ul>
            
            <!-- Language Switcher -->
            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" 
                        id="languageDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-globe me-1"></i>
                    {{ session('locale', 'en') == 'id' ? 'ID' : 'EN' }}
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
            </div>
        </div>
    </div>
</nav>