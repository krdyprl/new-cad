<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if there's booking data in session (user was trying to book before login)
        if ($request->session()->has('booking_data')) {
            return redirect()->route('booking.process-after-login');
        }

        // Check if there's a specific redirect request
        if ($request->session()->has('redirect_to')) {
            $redirectTo = $request->session()->pull('redirect_to');
            if ($redirectTo === 'booking') {
                return redirect()->route('frontend.booking')->with('success', 'Login berhasil! Silakan lanjutkan pemesanan Anda.');
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
