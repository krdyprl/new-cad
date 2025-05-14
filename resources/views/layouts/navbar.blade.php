{{-- Frontend design layer — loaded here so it wins the cascade over master.css on every page. --}}
<link href="{{ asset('css/ceramic.css') }}" rel="stylesheet">

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
                    <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                       href="{{ route('frontend.home') }}">
                       {{ __('messages.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.information') ? 'active' : '' }}" 
                       href="{{ route('frontend.information') }}">
                       {{ __('messages.information') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.catalog') ? 'active' : '' }}" 
                       href="{{ route('frontend.catalog') }}">
                       {{ __('messages.catalog') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.booking') ? 'active' : '' }}" 
                       href="{{ route('frontend.booking') }}">
                       {{ __('messages.booking') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://ths.li/I0zPHco" target="_blank">
                       {{ __('messages.virtual_tour') }}
                    </a>
                </li>
            </ul>
            
            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <!-- My Bookings Link -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('booking.my-bookings') ? 'active' : '' }}" 
                           href="{{ route('booking.my-bookings') }}">
                            <i class="fas fa-history me-1"></i>{{ __('messages.my_bookings') }}
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>{{ Str::limit(Auth::user()->name, 15) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(Auth::user()->is_admin)
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-crown me-2"></i>{{ __('messages.admin_panel') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i>{{ __('messages.profile') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
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
                            <i class="fas fa-sign-in-alt me-1"></i>{{ __('messages.login') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                           href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>{{ __('messages.register') }}
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
/* Navbar — light/cream, fixed */
.navbar {
    background: rgba(247, 242, 233, 0.82) !important;
    backdrop-filter: blur(12px);
    box-shadow: 0 1px 0 rgba(42, 33, 27, 0.07);
    padding: 0.6rem 0;
    transition: all 0.3s ease;
    z-index: 1050;
    width: 100%;
}

.navbar-brand { padding: 0.4rem 0; }
.navbar-logo {
    height: 52px;
    width: auto;
    filter: brightness(0) saturate(100%);
    opacity: 0.82;
    transition: all 0.3s ease;
}
.navbar-logo:hover { transform: scale(1.05); }

.nav-link {
    color: var(--kiln-ink-soft) !important;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 500;
    font-size: 0.92rem;
    letter-spacing: 0.01em;
    padding: 0.5rem 0.9rem !important;
    text-transform: none;
    transition: all 0.2s ease;
    border-radius: 6px;
    margin: 0 0.1rem;
}
.nav-link:hover { color: var(--glaze) !important; background: transparent; }
.nav-link.active { color: var(--kiln-ink) !important; background: transparent; font-weight: 600; }

.dropdown-menu {
    background: #fff;
    border: 1px solid var(--line);
    box-shadow: 0 18px 40px -22px rgba(42, 33, 27, 0.4);
}
.dropdown-item { color: var(--kiln-ink-soft) !important; transition: all 0.2s ease; }
.dropdown-item:hover { background: rgba(46, 110, 97, 0.08); color: var(--glaze) !important; }

.btn-outline-light {
    border-color: var(--line);
    color: var(--kiln-ink-soft);
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    background: transparent;
}
.btn-outline-light:hover {
    background: rgba(46, 110, 97, 0.08);
    border-color: var(--glaze);
    color: var(--glaze);
}

.navbar-toggler { border: 1.5px solid var(--line); border-radius: 8px; padding: 7px 11px; }
.navbar-toggler:hover { background: rgba(46, 110, 97, 0.08); }
.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2842, 33, 27, 0.7%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Mobile */
@media (max-width: 991px) {
    .navbar-collapse {
        background: rgba(251, 248, 242, 0.99);
        border-radius: 12px;
        margin-top: 1rem;
        padding: 1.2rem;
        border: 1px solid var(--line);
        box-shadow: 0 20px 44px -24px rgba(42, 33, 27, 0.4);
    }
    .nav-link { text-align: center; margin: 0.2rem 0; padding: 0.7rem 1rem !important; }
    .navbar-logo { height: 48px; }
}
@media (max-width: 576px) {
    .navbar-logo { height: 44px; }
    .nav-link { font-size: 0.95rem; }
}

body { padding-top: 0 !important; margin-top: 76px; }
</style>

{{-- ponytail: ~15 lines vanilla JS for navbar-shrink + scroll-reveal; no library needed. --}}
<script>
(function () {
  var nav = document.querySelector('.navbar');
  addEventListener('scroll', function () {
    nav.classList.toggle('scrolled', scrollY > 40);
  }, { passive: true });

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: 0.12 });
  addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
  });
})();
</script>

{{-- Floating chat widget on every page (except the dedicated chatbot page, which IS the chat) --}}
@unless(request()->routeIs('frontend.chatbot') || request()->boolean('embed'))
    @include('layouts.chat-widget')
@endunless
