<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        // Only require auth for PDF generation and my bookings
        // Allow guest to submit booking (auth check will be done manually in submitBooking method)
        $this->middleware('auth')->only(['generatePDF', 'myBookings']);
        
        Log::info('BookingController initialized', [
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Get user's bookings with enhanced logging
     */
    public function myBookings()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            Log::info('User accessing my bookings page', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);
            
            $bookings = $user->bookings()->orderBy('created_at', 'desc')->paginate(10);
            
            Log::info('My bookings data retrieved', [
                'user_id' => $user->id,
                'total_bookings' => $bookings->total(),
                'current_page' => $bookings->currentPage()
            ]);
            
            return view('booking.my-bookings', compact('bookings'));
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user bookings', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal memuat data pemesanan.');
        }
    }    /**
     * Generate PDF with enhanced error handling and logging
     */
    public function generatePDF($id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            Log::info('PDF generation requested', [
                'booking_id' => $id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);
            
            $booking = $user->bookings()->findOrFail($id);
            
            // Check if PDF already exists
            if ($booking->pdf_file && Storage::disk('public')->exists('invoices/' . $booking->pdf_file)) {
                Log::info('Existing PDF file served', [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_id,
                    'pdf_file' => $booking->pdf_file,
                    'user_id' => $user->id
                ]);
                
                return response()->file(storage_path('app/public/invoices/' . $booking->pdf_file));
            }
            
            // If PDF doesn't exist, regenerate it
            Log::info('Regenerating PDF file', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'reason' => 'PDF file not found'
            ]);
            
            $pdf = $this->generateInvoicePDF($booking->toArray());
            $fileName = 'invoice-' . $booking->booking_id . '.pdf';
            Storage::disk('public')->put('invoices/' . $fileName, $pdf->output());
            
            $booking->update(['pdf_file' => $fileName]);
            
            Log::info('PDF regenerated successfully', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_id,
                'pdf_file' => $fileName
            ]);
            
            return response()->file(storage_path('app/public/invoices/' . $fileName));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Booking not found for PDF generation', [
                'booking_id' => $id,
                'user_id' => auth()->id(),
                'error' => 'Booking not found or not owned by user'
            ]);
            
            return abort(404, 'Booking not found.');
            
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'booking_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Gagal menghasilkan PDF. Silakan coba lagi.');
        }
    }    /**
     * Submit booking with comprehensive validation and logging
     */
    public function submitBooking(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            Log::info('Booking submission started', [
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'is_authenticated' => Auth::check(),
                'user_id' => auth()->id()
            ]);

            // Check if user is authenticated, if not redirect to login with form data
            if (!Auth::check()) {
                Log::info('Unauthenticated booking attempt - redirecting to login', [
                    'ip_address' => $request->ip(),
                    'package' => $request->input('package'),
                    'participants' => $request->input('participants'),
                    'email' => $request->input('email')
                ]);
                
                // Store booking data in session
                $request->session()->put('booking_data', $request->all());
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Silakan login atau daftar terlebih dahulu untuk melanjutkan pemesanan.',
                        'redirect_to_login' => true,
                        'login_url' => route('login') . '?redirect_to=booking'
                    ], 401);
                }
                
                return redirect()->route('login')
                    ->with('info', 'Silakan login atau daftar terlebih dahulu untuk melanjutkan pemesanan.')
                    ->with('redirect_to', 'booking');
            }

            // Enhanced validation with detailed logging
            Log::info('Starting booking validation', [
                'user_id' => Auth::id(),
                'input_data' => $this->getSafeInputForLog($request)
            ]);

            $validated = $request->validate([
                'package' => 'required|string',
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'participants' => 'required|integer|min:1|max:50',
                'visitDate' => 'required|date|after_or_equal:today',
                'visitTime' => 'required|string',
                'notes' => 'nullable|string|max:1000',
            ], [
                'package.required' => 'Paket workshop wajib dipilih.',
                'fullName.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'participants.required' => 'Jumlah peserta wajib diisi.',
                'participants.min' => 'Jumlah peserta minimal 1 orang.',
                'participants.max' => 'Jumlah peserta maksimal 50 orang.',
                'visitDate.required' => 'Tanggal kunjungan wajib dipilih.',
                'visitDate.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
                'visitTime.required' => 'Waktu kunjungan wajib dipilih.',
                'notes.max' => 'Catatan maksimal 1000 karakter.',
            ]);

            Log::info('Booking validation passed', [
                'user_id' => Auth::id(),
                'validated_data' => $this->getSafeValidatedDataForLog($validated)
            ]);

            // Calculate price based on package and participants
            $priceCalculation = $this->calculatePrice($validated['package'], $validated['participants']);
            
            Log::info('Price calculation completed', [
                'package' => $validated['package'],
                'participants' => $validated['participants'],
                'price_calculation' => $priceCalculation
            ]);
            
            // Generate unique booking ID
            $bookingId = 'CAD-' . date('Ymd') . '-' . rand(1000, 9999);
            
            // Start database transaction
            DB::beginTransaction();
            
            try {
                // Save booking to database
                $booking = Booking::create([
                    'booking_id' => $bookingId,
                    'user_id' => Auth::id(),
                    'package' => $validated['package'],
                    'package_name' => $priceCalculation['service_name'],
                    'full_name' => $validated['fullName'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'participants' => $validated['participants'],
                    'visit_date' => $validated['visitDate'],
                    'visit_time' => $validated['visitTime'],
                    'notes' => $validated['notes'] ?? '',
                    'price_per_unit' => $priceCalculation['price_per_unit'],
                    'subtotal' => $priceCalculation['subtotal'],
                    'tax' => $priceCalculation['tax'],
                    'total' => $priceCalculation['total'],
                    'status' => BookingStatus::PENDING
                ]);
                
                Log::info('Booking created in database', [
                    'booking_id' => $booking->id,
                    'booking_code' => $bookingId,
                    'user_id' => Auth::id(),
                    'total_amount' => $priceCalculation['total']
                ]);
                
                // Log the booking activity
                $booking->logActivity('booking_created', [
                    'package' => $validated['package'],
                    'participants' => $validated['participants'],
                    'visit_date' => $validated['visitDate'],
                    'total_amount' => $priceCalculation['total']
                ]);
                
                // Prepare data for invoice (using booking data)
                $invoiceData = $booking->toArray();
                $invoiceData['name'] = $booking->full_name;
                $invoiceData['service'] = $booking->package;
                $invoiceData['service_name'] = $booking->package_name;
                $invoiceData['created_at'] = $booking->created_at;

                // Generate PDF invoice
                Log::info('Starting PDF generation', ['booking_id' => $booking->id]);
                
                $pdf = $this->generateInvoicePDF($invoiceData);
                
                // Save PDF to storage
                $fileName = 'invoice-' . $bookingId . '.pdf';
                $filePath = 'invoices/' . $fileName;
                Storage::disk('public')->put($filePath, $pdf->output());
                
                // Update booking with PDF file name
                $booking->update(['pdf_file' => $fileName]);
                
                Log::info('PDF generated and saved', [
                    'booking_id' => $booking->id,
                    'pdf_file' => $fileName,
                    'file_size' => Storage::disk('public')->size($filePath)
                ]);
                
                // Generate WhatsApp URL and send directly to admin
                $whatsappUrl = $this->sendToWhatsAppAdmin($invoiceData, $fileName);
                
                // Commit the transaction
                DB::commit();
                
                // Log successful booking completion
                $endTime = microtime(true);
                $processingTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
                
                Log::info('Booking submitted successfully', [
                    'booking_id' => $booking->id,
                    'booking_code' => $bookingId,
                    'user_id' => Auth::id(),
                    'processing_time_ms' => $processingTime,
                    'pdf_generated' => true,
                    'whatsapp_url_generated' => !empty($whatsappUrl)
                ]);

                // Return JSON response with WhatsApp URL for automatic redirect
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pemesanan berhasil! Anda akan diarahkan ke WhatsApp admin.',
                        'whatsapp_url' => $whatsappUrl,
                        'booking_id' => $bookingId,
                        'booking_id' => $bookingId,
                        'pdf_url' => route('frontend.booking.pdf', $booking->id)
                    ]);
                }

                // For regular form submission, redirect to WhatsApp
                return redirect()->away($whatsappUrl);

            } catch (\Exception $dbException) {
                DB::rollBack();
                throw $dbException;
            }

        } catch (ValidationException $e) {
            Log::warning('Booking validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'input' => $this->getSafeInputForLog($request)
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // Rollback transaction if it's still active
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('Booking submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'processing_time_ms' => $processingTime,
                'input' => $this->getSafeInputForLog($request)
            ]);

            $message = 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()
                ->with('error', $message)
                ->withInput();
        }
    }

    /**
     * Get safe input data for logging (removing sensitive information)
     */
    private function getSafeInputForLog(Request $request): array
    {
        $input = $request->all();
        
        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'token'];
        foreach ($sensitiveFields as $field) {
            unset($input[$field]);
        }
        
        return $input;
    }

    /**
     * Get safe validated data for logging
     */
    private function getSafeValidatedDataForLog(array $validated): array
    {
        // All validated booking data is safe to log
        return $validated;
    }

    /**
     * Calculate price based on service and participants
     */
    private function calculatePrice($service, $participants)
    {
        $prices = [
            'basic' => ['price' => 50000, 'name' => 'Paket Basic'],
            'premium' => ['price' => 85000, 'name' => 'Paket Premium'],
            'family' => ['price' => 200000, 'name' => 'Paket Family (4 orang)'],
            'group' => ['price' => 400000, 'name' => 'Paket Group (10 orang)'],
        ];

        $serviceData = $prices[$service] ?? $prices['basic'];
        
        // Calculate total based on package type
        if ($service === 'family' || $service === 'group') {
            // Fixed price packages
            $subtotal = $serviceData['price'];
        } else {
            // Per person packages
            $subtotal = $serviceData['price'] * $participants;
        }
        
        $tax = round($subtotal * 0.11); // 11% PPN
        $total = $subtotal + $tax;

        return [
            'service_name' => $serviceData['name'],
            'price_per_unit' => $serviceData['price'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    private function generateInvoicePDF($data)
    {
        // Generate PDF using the invoice blade template
        $pdf = PDF::loadView('booking-invoice', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }

    private function sendToWhatsAppAdmin($data, $fileName)
    {
        $adminPhoneNumber = '62895397307475'; // Admin WhatsApp number (without +)
        
        // Generate download URL for the PDF
        $pdfUrl = url('storage/invoices/' . $fileName);
        
        // Format the WhatsApp message
        $message = "🏺 *PEMESANAN WORKSHOP BARU - CAD* 🏺\n\n";
        $message .= "📋 *DETAIL PEMESANAN*\n";
        $message .= "• ID Booking: {$data['booking_id']}\n";
        $message .= "• Nama: {$data['name']}\n";
        $message .= "• Email: {$data['email']}\n";
        $message .= "• No. HP: {$data['phone']}\n";
        $message .= "• Paket: {$data['service_name']}\n";
        $message .= "• Jumlah Peserta: {$data['participants']} orang\n";
        $message .= "• Tanggal: " . date('d F Y', strtotime($data['visit_date'])) . "\n";
        $message .= "• Waktu: {$data['visit_time']} WIB\n";
        
        if (!empty($data['notes'])) {
            $message .= "• Catatan: {$data['notes']}\n";
        }
        
        $message .= "\n💰 *TOTAL PEMBAYARAN*\n";
        $message .= "Rp " . number_format($data['total'], 0, ',', '.') . "\n\n";
        $message .= "📄 *Invoice PDF:*\n";
        $message .= $pdfUrl . "\n\n";
        $message .= "⏰ Mohon segera konfirmasi ketersediaan jadwal.\n";
        $message .= "Terima kasih! 🙏\n\n";
        $message .= "_Pesan otomatis dari website CAD_";

        // Encode message for URL
        $encodedMessage = urlencode($message);
          // Generate WhatsApp URL
        $whatsappUrl = "https://wa.me/{$adminPhoneNumber}?text={$encodedMessage}";

        return $whatsappUrl;
    }
    /**
     * Process booking after user login/register with enhanced logging
     */
    public function processAfterLogin(Request $request)
    {
        Log::info('Processing booking after login started', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'timestamp' => now(),
            'ip_address' => $request->ip()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('Unauthenticated access to processAfterLogin', [
                'session_id' => session()->getId(),
                'ip_address' => $request->ip()
            ]);
            
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get booking data from session
        $bookingData = $request->session()->get('booking_data');
        
        if (!$bookingData) {
            Log::warning('No booking data found in session after login', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);
            
            return redirect()->route('frontend.booking')
                ->with('error', 'Data booking tidak ditemukan. Silakan isi form booking kembali.');
        }

        Log::info('Booking data retrieved from session', [
            'user_id' => Auth::id(),
            'booking_data' => $this->getSafeBookingDataForLog($bookingData)
        ]);

        // Create a new request with booking data
        $bookingRequest = new Request();
        $bookingRequest->replace($bookingData);
        
        // Share the session with the new request
        if ($request->hasSession()) {
            $bookingRequest->setLaravelSession($request->session());
        }

        // Process the booking
        try {
            Log::info('Attempting to process stored booking data', [
                'user_id' => Auth::id(),
                'package' => $bookingData['package'] ?? 'unknown'
            ]);
            
            $response = $this->submitBooking($bookingRequest);
            
            // Clear booking data from session
            $request->session()->forget('booking_data');
            
            Log::info('Booking processed successfully after login', [
                'user_id' => Auth::id(),
                'session_cleared' => true
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Booking process after login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'booking_data' => $this->getSafeBookingDataForLog($bookingData)
            ]);
            
            return redirect()->route('frontend.booking')
                ->with('error', 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.')
                ->withInput($bookingData);
        }
    }

    /**
     * Get safe booking data for logging
     */
    private function getSafeBookingDataForLog(array $bookingData): array
    {
        // Remove any sensitive data if present
        $safe = $bookingData;
        unset($safe['password'], $safe['token'], $safe['_token']);
        return $safe;
    }
}