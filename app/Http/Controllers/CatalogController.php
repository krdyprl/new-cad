<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class CatalogController extends Controller
{
    /**
     * Display catalog page with products
     */
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $search = $request->input('search');
        
        try {
            $query = Product::active();
            
            // Apply category filter
            if ($category) {
                $query->where('category', $category);
            }
            
            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('specifications', 'like', "%{$search}%");
                });
            }
            
            $products = $query->orderBy('created_at', 'desc')->paginate(12);
            
            // Get categories for filter
            $categories = Product::active()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');
                
        } catch (\Exception $e) {
            // Fallback: Use dummy data when database is not available
            Log::warning('Database not available, using dummy data for catalog', ['error' => $e->getMessage()]);
            
            $products = $this->getDummyProducts();
            $categories = collect(['Guci', 'Mangkuk', 'Piring', 'Vas', 'Souvenir']);
        }
        
        return view('catalog', compact('products', 'categories', 'category', 'search'));
    }
    
    /**
     * Get dummy products for fallback
     */
    private function getDummyProducts()
    {
        $dummyData = collect([
            $this->createDummyProduct(1, 'Guci Keramik Tradisional', 'Guci', 150000, 'Guci air dengan desain tradisional Jawa. Praktis untuk menyimpan air minum dan menjaga suhu tetap sejuk.', 'img/products/guci1.jpg'),
            $this->createDummyProduct(2, 'Mangkuk Sup Keramik', 'Mangkuk', 45000, 'Mangkuk sup dengan desain ergonomis dan bahan berkualitas tinggi. Cocok untuk hidangan berkuah.', 'img/products/mangkuk1.jpg'),
            $this->createDummyProduct(3, 'Piring Saji Batik', 'Piring', 75000, 'Piring saji dengan motif batik khas Jawa Timur. Cocok untuk acara formal maupun casual.', 'img/products/piring1.jpg'),
            $this->createDummyProduct(4, 'Vas Bunga Minimalis', 'Vas', 120000, 'Vas bunga dengan desain minimalis modern. Cocok untuk dekorasi rumah atau kantor.', 'img/products/vas1.jpg')
        ]);
        
        // Simple pagination simulation
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $dummyData,
            $dummyData->count(),
            12,
            1,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }
    
    /**
     * Create a dummy product object with all required methods
     */
    private function createDummyProduct($id, $name, $category, $price, $description, $image)
    {
        return new class($id, $name, $category, $price, $description, $image) {
            public $id;
            public $name;
            public $category;
            public $price;
            public $description;
            public $image;
            public $is_active = true;
            
            public function __construct($id, $name, $category, $price, $description, $image)
            {
                $this->id = $id;
                $this->name = $name;
                $this->category = $category;
                $this->price = $price;
                $this->description = $description;
                $this->image = $image;
            }
            
            public function hasValidImage()
            {
                return !empty($this->image);
            }
            
            public function getImageUrl()
            {
                return asset($this->image);
            }
            
            public function getFormattedPriceAttribute()
            {
                return 'Rp ' . number_format($this->price, 0, ',', '.');
            }
            
            public function __get($name)
            {
                if ($name === 'formatted_price') {
                    return $this->getFormattedPriceAttribute();
                }
                return null;
            }
        };
    }
    
    /**
     * Show product detail
     */
    public function show($id): View
    {
        try {
            // Try to get product from database
            $product = Product::active()->findOrFail($id);
            
            // Get related products from same category
            $relatedProducts = Product::active()
                ->where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get();
                
        } catch (\Exception $e) {
            // Fallback when database is not available
            Log::warning('Database not available for product detail', ['error' => $e->getMessage()]);
            
            $relatedProducts = collect();
            
            // Create dummy product
            $product = $this->createDummyProduct(
                $id, 
                'Sample Product', 
                'Sample', 
                100000, 
                'This is a sample product description for demonstration purposes.', 
                'img/products/sample.jpg'
            );
        }
        
        return view('catalog-detail', compact('product', 'relatedProducts'));
    }
}
