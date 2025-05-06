<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main routes
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Frontend routes
Route::get('/about', function () {
    try {
        return view('about');
    } catch (\Exception $e) {
        Log::error('About page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Page not available.');
    }
})->name('frontend.about');

Route::get('/information', [InformationController::class, 'index'])->name('frontend.information');
Route::get('/information/{id}', [InformationController::class, 'show'])->name('frontend.information.show');

Route::get('/test-catalog', function () {
    return 'Catalog route test - OK';
});

Route::get('/catalog', [CatalogController::class, 'index'])->name('frontend.catalog');
Route::get('/catalog/{product}', [CatalogController::class, 'show'])->name('frontend.catalog.show');

Route::get('/booking', function () {
    try {
        // Allow everyone to access booking form
        return view('booking');
    } catch (\Exception $e) {
        Log::error('Booking page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Page not available.');
    }
})->name('frontend.booking');

Route::get('/chatbot', function () {
    try {
        return view('chatbot-main');
    } catch (\Exception $e) {
        Log::error('Chatbot page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Page not available.');
    }
})->name('frontend.chatbot');

Route::get('/chatbot-huggingface', function () {
    try {
        return view('chatbot-huggingface');
    } catch (\Exception $e) {
        Log::error('Chatbot Hugging Face page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Page not available.');
    }
})->name('frontend.chatbot.huggingface');

Route::get('/chatbot-groq', function () {
    try {
        return view('chatbot-groq');
    } catch (\Exception $e) {
        Log::error('Chatbot Groq page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Page not available.');
    }
})->name('frontend.chatbot.groq');

// Demo route for testing styling
Route::get('/demo-styling', function () {
    try {
        return view('demo-styling');
    } catch (\Exception $e) {
        Log::error('Demo styling page error', ['error' => $e->getMessage()]);
        return redirect()->route('frontend.home')->with('error', 'Demo page not available.');
    }
})->name('frontend.demo');

// Form submissions with CSRF protection
Route::post('/booking', [BookingController::class, 'submitBooking'])
    ->name('frontend.booking.submit')
    ->middleware(['throttle:5,1']); // Remove auth middleware, check in controller instead

// PDF generation route (requires authentication)
Route::get('/booking/pdf/{id}', [BookingController::class, 'generatePDF'])
    ->name('frontend.booking.pdf')
    ->middleware(['auth']); // Only authenticated users can generate PDF

Route::post('/chatbot', function (Request $request) {
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\+\d\s\-\(\)]+$/',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        // Log chatbot form submission
        Log::info('Chatbot form submitted', $validated);

        $message = app()->getLocale() === 'id' 
            ? 'Terima kasih atas pesan Anda! Kami akan segera menghubungi Anda.' 
            : 'Thank you for your message! We will contact you soon.';

        return redirect()->back()->with('success', $message);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
    } catch (\Exception $e) {
        Log::error('Chatbot form error', [
            'error' => $e->getMessage(),
            'data' => $request->all()
        ]);

        $message = app()->getLocale() === 'id' 
            ? 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.' 
            : 'An error occurred while sending your message. Please try again.';

        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }
})->name('frontend.chatbot.submit')->middleware(['throttle:3,1']);

// Language routes
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])
    ->name('language.switch')
    ->where('locale', 'en|id');

Route::get('/lang/{locale}', function ($locale) {
    try {
        if (in_array($locale, ['en', 'id'])) {
            session(['locale' => $locale]);
            return redirect()->back()->with('success', 'Language changed successfully.');
        }
        
        return redirect()->back()->with('error', 'Invalid language selection.');
    } catch (\Exception $e) {
        Log::error('Language switch error', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Failed to change language.');
    }
})->name('lang.switch')->where('locale', 'en|id');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        try {
            return view('dashboard');
        } catch (\Exception $e) {
            Log::error('Dashboard error', ['error' => $e->getMessage()]);
            return redirect()->route('frontend.home')->with('error', 'Dashboard not available.');
        }
    })->name('dashboard');    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
      // My Bookings route
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('booking.my-bookings');
      // Process booking after login
    Route::post('/booking/process-after-login', [BookingController::class, 'processAfterLogin'])->name('booking.process-after-login');
});

// Test routes for debugging (NO MIDDLEWARE)
Route::get('/admin/test-minimal', [App\Http\Controllers\AdminController::class, 'testMinimal'])->name('admin.test.minimal');
Route::get('/test-simple', function() {
    return response()->json([
        'message' => 'Simple test route is working!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
})->name('test.simple');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Bookings Management
    Route::get('/bookings', [App\Http\Controllers\AdminController::class, 'bookings'])->name('bookings');
    Route::patch('/bookings/{booking}/status', [App\Http\Controllers\AdminController::class, 'updateBookingStatus'])->name('bookings.status');
    Route::delete('/bookings/{booking}', [App\Http\Controllers\AdminController::class, 'deleteBooking'])->name('bookings.delete');
    
    // Information Management
    Route::get('/information', [App\Http\Controllers\AdminController::class, 'information'])->name('information');
    Route::get('/information/create', [App\Http\Controllers\AdminController::class, 'createInformation'])->name('information.create');
    Route::post('/information', [App\Http\Controllers\AdminController::class, 'storeInformation'])->name('information.store');
    Route::get('/information/{information}/edit', [App\Http\Controllers\AdminController::class, 'editInformation'])->name('information.edit');
    Route::patch('/information/{information}', [App\Http\Controllers\AdminController::class, 'updateInformation'])->name('information.update');
    Route::delete('/information/{information}', [App\Http\Controllers\AdminController::class, 'deleteInformation'])->name('information.delete');
    
    // Products Management
    Route::get('/products', [App\Http\Controllers\AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [App\Http\Controllers\AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\AdminController::class, 'editProduct'])->name('products.edit');
    Route::patch('/products/{product}', [App\Http\Controllers\AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('products.delete');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/download/{type}', [App\Http\Controllers\AdminController::class, 'downloadReport'])->name('reports.download');
      // Settings
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
});

// Error handling for 404
Route::fallback(function () {
    return view('errors.404');
});

require __DIR__.'/auth.php';
