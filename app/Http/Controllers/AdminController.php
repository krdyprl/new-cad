<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Information;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

/**
 * Admin Controller for managing admin panel functionality
 * 
 * This controller handles all admin-related operations including:
 * - Dashboard statistics and overview
 * - User management
 * - Booking management
 * - Information/content management
 */
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display admin dashboard with statistics
     */
    public function dashboard(): View
    {
        try {
            // ponytail: 4 COUNTs cached 60s — counters don't need to be real-time.
            $stats = Cache::remember('admin.dashboard.stats', 60, fn () => [
                'totalUsers' => User::count(),
                'totalBookings' => Booking::count(),
                'pendingBookings' => Booking::where('status', 'pending')->count(),
                'totalInformation' => Information::count(),
            ]);
            extract($stats);

            // Recent bookings
            $recentBookings = Booking::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalUsers',
                'totalBookings', 
                'pendingBookings',
                'totalInformation',
                'recentBookings'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard error', ['error' => $e->getMessage()]);
            return view('admin.dashboard')->with('error', 'Unable to load dashboard data');
        }
    }

    /**
     * Display users management
     */
    public function users(): View
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user): RedirectResponse
    {
        try {
            // Prevent admin from deleting themselves
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
            }

            $user->delete();
            
            return redirect()->back()->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus user!');
        }
    }

    /**
     * Display bookings management
     */
    public function bookings(): View
    {
        $bookings = Booking::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        try {
            $booking->update(['status' => $request->status]);
            
            return redirect()->back()->with('success', 'Status booking berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating booking status', [
                'booking_id' => $booking->id, 
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate status!');
        }
    }

    /**
     * Delete booking
     */
    public function deleteBooking(Booking $booking): RedirectResponse
    {
        try {
            $booking->delete();
            
            return redirect()->back()->with('success', 'Booking berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting booking', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus booking!');
        }
    }

    /**
     * Display information management
     */
    public function information(): View
    {
        $information = Information::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.information.index', compact('information'));
    }

    /**
     * Show create information form
     */
    public function createInformation(): View
    {
        return view('admin.information.create');
    }

    /**
     * Store new information
     */
    public function storeInformation(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->all();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/information', $imageName);
                $data['image'] = 'storage/information/' . $imageName;
            }

            Information::create($data);
            
            return redirect()->route('admin.information')->with('success', 'Informasi berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('Error creating information', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat informasi!');
        }
    }

    /**
     * Show edit information form
     */
    public function editInformation(Information $information): View
    {
        return view('admin.information.edit', compact('information'));
    }

    /**
     * Update information
     */
    public function updateInformation(Request $request, Information $information): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->all();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($information->image && file_exists(public_path($information->image))) {
                    unlink(public_path($information->image));
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/information', $imageName);
                $data['image'] = 'storage/information/' . $imageName;
            }

            $information->update($data);
            
            return redirect()->route('admin.information')->with('success', 'Informasi berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating information', [
                'information_id' => $information->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate informasi!');
        }
    }

    /**
     * Delete information
     */
    public function deleteInformation(Information $information): RedirectResponse
    {
        try {
            $information->delete();
            
            return redirect()->back()->with('success', 'Informasi berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting information', [
                'information_id' => $information->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus informasi!');
        }
    }

    /**
     * Display reports
     */
    public function reports(): View
    {
        $bookingStats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        $userStats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'regular' => User::where('is_admin', false)->count(),
        ];

        // Calculate revenue from completed bookings
        $totalRevenue = Booking::where('status', 'completed')->sum('total') ?? 0;
        $monthlyRevenue = Booking::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total') ?? 0;

        // Get popular packages data
        $popularPackages = Booking::select('package')
            ->selectRaw('COUNT(*) as count')
            ->where('status', 'completed')
            ->groupBy('package')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // Get monthly booking data for chart (last 12 months)
        $monthlyBookingData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyBookingData[$date->format('M Y')] = Booking::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', 'completed')
                ->count();
        }

        return view('admin.reports', compact(
            'bookingStats', 
            'userStats', 
            'totalRevenue', 
            'monthlyRevenue',
            'popularPackages',
            'monthlyBookingData'
        ));
    }

    /**
     * Download report
     */
    public function downloadReport(string $type): RedirectResponse
    {
        // TODO: Implement report download functionality
        return redirect()->back()->with('info', 'Download report functionality coming soon!');
    }

    /**
     * Display settings
     */
    public function settings(): View
    {
        // TODO: Implement settings functionality
        return view('admin.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        // TODO: Implement settings update functionality
        return redirect()->back()->with('info', 'Settings functionality coming soon!');
    }

    /**
     * Check user admin status safely
     */
    private function checkUserAdminStatus($user): bool
    {
        try {
            if (method_exists($user, 'isAdmin') && is_callable([$user, 'isAdmin'])) {
                return (bool) call_user_func([$user, 'isAdmin']);
            }
            return (bool) ($user->is_admin ?? false);
        } catch (\Exception $e) {
            return (bool) ($user->is_admin ?? false);
        }
    }

    /**
     * Test method for debugging (NO MIDDLEWARE)
     */
    public function testMinimal()
    {
        try {
            $user = auth()->user();
            return response()->json([
                'message' => 'Admin controller is working!',
                'authenticated' => auth()->check(),
                'user' => $user ? [
                    'id' => $user->id,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin ?? null,
                    'isAdmin()' => $this->checkUserAdminStatus($user)
                ] : null,
                'timestamp' => now(),
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in test method',
                'error' => $e->getMessage(),
                'timestamp' => now(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Display products list
     */
    public function products(): View
    {
        try {
            // Check if products table exists
            if (!Schema::hasTable('products')) {
                Log::error('Products table does not exist');
                return view('admin.products.index')
                    ->with('products', collect())
                    ->with('error', 'Products table not found. Please run migrations.');
            }

            $products = Product::orderBy('created_at', 'desc')->paginate(10);
            return view('admin.products.index', compact('products'));
        } catch (\Exception $e) {
            Log::error('Error loading products', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return with empty collection for debugging
            $products = collect();
            return view('admin.products.index', compact('products'))
                ->with('error', 'Error loading products: ' . $e->getMessage());
        }
    }

    /**
     * Show create product form
     */
    public function createProduct(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store new product
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'price' => 'required|numeric|min:0|max:9999999999999', // Maksimal 13 digit (sesuai decimal(15,2))
                'description' => 'required|string',
                'specifications' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'ecommerce_link' => 'nullable|url',
                'is_active' => 'boolean'
            ]);

            $productData = $request->except('image');
            $productData['is_active'] = $request->boolean('is_active', true);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Validate image file
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'File gambar tidak valid!');
                }
                
                // Create products directory if it doesn't exist
                if (!Storage::exists('public/products')) {
                    Storage::makeDirectory('public/products');
                }
                
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $image->getClientOriginalName());
                $imagePath = $image->storeAs('public/products', $imageName);
                
                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal menyimpan gambar!');
                }
                
                $productData['image'] = 'storage/products/' . $imageName;
            }

            Product::create($productData);

            return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('Error storing product', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['image'])
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage());
        }
    }

    /**
     * Show edit product form
     */
    public function editProduct(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'price' => 'required|numeric|min:0|max:9999999999999', // Maksimal 13 digit (sesuai decimal(15,2))
                'description' => 'required|string',
                'specifications' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'ecommerce_link' => 'nullable|url',
                'is_active' => 'boolean'
            ]);

            $productData = $request->except('image');
            $productData['is_active'] = $request->boolean('is_active', true);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Validate image file
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'File gambar tidak valid!');
                }
                
                // Create products directory if it doesn't exist
                if (!Storage::exists('public/products')) {
                    Storage::makeDirectory('public/products');
                }
                
                // Delete old image if exists
                if ($product->image && str_starts_with($product->image, 'storage/')) {
                    $oldImagePath = str_replace('storage/', 'public/', $product->image);
                    if (Storage::exists($oldImagePath)) {
                        Storage::delete($oldImagePath);
                    }
                }

                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $image->getClientOriginalName());
                $imagePath = $image->storeAs('public/products', $imageName);
                
                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal menyimpan gambar!');
                }
                
                $productData['image'] = 'storage/products/' . $imageName;
            }

            $product->update($productData);

            return redirect()->route('admin.products')->with('success', 'Produk berhasil diupdate!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'product_id' => $product->id,
                'request_data' => $request->except(['image'])
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate produk: ' . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product): RedirectResponse
    {
        // Delete image if exists
        if ($product->image && str_starts_with($product->image, 'storage/')) {
            $imagePath = str_replace('storage/', 'public/', $product->image);
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus!');
    }
}
