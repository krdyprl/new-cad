<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Check if there's booking data in session (user was trying to book before register)
        if ($request->session()->has('booking_data')) {
            return redirect()->route('booking.process-after-login');
        }

        // Check if there's a specific redirect request
        if ($request->session()->has('redirect_to')) {
            $redirectTo = $request->session()->pull('redirect_to');
            if ($redirectTo === 'booking') {
                return redirect()->route('frontend.booking')->with('success', 'Registrasi berhasil! Silakan lanjutkan pemesanan Anda.');
            }
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
