<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\PaymentService;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

echo "=== AUDIT KHUSUS: REAL VS TEST DISCOUNT ===\n\n";

try {
    // 1. Setup data untuk audit
    echo "1. SETUP DATA AUDIT:\n";
    
    $user = User::where('email', 'student@example.com')->first();
    if (!$user) {
        // Gunakan user yang sudah ada atau buat manual
        $user = User::first(); // Ambil user pertama yang ada
        if (!$user) {
            echo "   ❌ Tidak ada user di database\n";
            exit(1);
        }
    }
    echo "   ✅ User: {$user->name} (ID: {$user->id})\n";
    
    $course = Course::where('name', 'Complete Laravel Development Course')->first();
    if (!$course) {
        echo "   ❌ Course tidak ditemukan\n";
        exit(1);
    }
    echo "   ✅ Course: {$course->name} (ID: {$course->id})\n";
    
    $discount = Discount::where('code', 'FLASH50')->where('is_active', true)->first();
    if (!$discount) {
        echo "   ❌ Discount FLASH50 tidak ditemukan\n";
        exit(1);
    }
    echo "   ✅ Discount: {$discount->name} (Code: {$discount->code})\n\n";
    
    // 2. SIMULASI ALUR TES (Yang berhasil)
    echo "2. SIMULASI ALUR TES (Yang berhasil):\n";
    
    // Clear session
    Session::flush();
    Auth::login($user);
    
    $transactionService = app(TransactionService::class);
    $discountService = app(DiscountService::class);
    
    // Prepare checkout
    $checkoutData = $transactionService->prepareCourseCheckout($course);
    echo "   ✅ Checkout prepared\n";
    
    // Apply discount
    $validation = $discountService->validateDiscountForCourse($discount->code, $course);
    if ($validation['valid']) {
        $transactionService->applyDiscount($validation['discount']);
        echo "   ✅ Discount applied to session\n";
        
        // Check session data
        $sessionDiscount = session('applied_discount');
        echo "   📊 Session discount data: " . json_encode($sessionDiscount, JSON_PRETTY_PRINT) . "\n";
    }
    
    // Simulate payment creation (TES)
    echo "\n   🔍 SIMULASI PAYMENT SERVICE (TES):\n";
    $paymentService = app(PaymentService::class);
    
    // Log session sebelum payment
    echo "   📊 Session sebelum payment:\n";
    echo "       - applied_discount: " . json_encode(session('applied_discount'), JSON_PRETTY_PRINT) . "\n";
    echo "       - course_id: " . session('course_id') . "\n";
    
    // Simulate createCoursePayment logic
    $appliedDiscount = session()->get('applied_discount');
    $discountAmount = 0;
    $discountId = null;
    
    if ($appliedDiscount) {
        $discountId = $appliedDiscount['id'] ?? null;
        
        if (isset($appliedDiscount['type']) && isset($appliedDiscount['value'])) {
            if ($appliedDiscount['type'] === 'percentage') {
                $discountAmount = ($course->price * $appliedDiscount['value']) / 100;
                if (isset($appliedDiscount['maximum_discount']) && $appliedDiscount['maximum_discount'] > 0) {
                    $discountAmount = min($discountAmount, $appliedDiscount['maximum_discount']);
                }
            } else {
                $discountAmount = min($appliedDiscount['value'], $course->price);
            }
        }
    }
    
    echo "   📊 Calculated discount amount: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
    echo "   📊 Discount ID: {$discountId}\n";
    
    // 3. SIMULASI ALUR REAL (Yang gagal)
    echo "\n3. SIMULASI ALUR REAL (Yang gagal):\n";
    
    // Clear session dan mulai fresh
    Session::flush();
    Auth::login($user);
    
    // Simulate frontend checkout process
    echo "   🌐 Simulating frontend checkout process...\n";
    
    // Step 1: User mengakses halaman checkout
    $checkoutData = $transactionService->prepareCourseCheckout($course);
    echo "   ✅ User mengakses checkout page\n";
    echo "   📊 Initial session course_id: " . session('course_id') . "\n";
    
    // Step 2: User apply discount via AJAX
    echo "\n   🔍 User apply discount via AJAX...\n";
    $validation = $discountService->validateDiscountForCourse($discount->code, $course);
    if ($validation['valid']) {
        $transactionService->applyDiscount($validation['discount']);
        echo "   ✅ Discount applied via AJAX\n";
        
        $sessionDiscount = session('applied_discount');
        echo "   📊 Session after AJAX: " . json_encode($sessionDiscount, JSON_PRETTY_PRINT) . "\n";
    }
    
    // Step 3: User klik Pay Now (simulate FrontController::paymentStoreCoursesMidtrans)
    echo "\n   💳 User klik Pay Now...\n";
    
    // Simulate request data dari frontend
    $frontendDiscountData = [
        'applied_discount' => [
            'id' => $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'type' => $discount->type,
            'value' => $discount->value
        ]
    ];
    
    echo "   📤 Frontend sends: " . json_encode($frontendDiscountData, JSON_PRETTY_PRINT) . "\n";
    
    // Simulate FrontController logic
    $request = new Request();
    $request->merge($frontendDiscountData);
    
    $courseId = session()->get('course_id');
    echo "   📊 Course ID from session: {$courseId}\n";
    
    // Handle applied discount from frontend request (seperti di FrontController)
    $appliedDiscountFromRequest = $request->input('applied_discount');
    if ($appliedDiscountFromRequest) {
        echo "   🔍 Processing discount from frontend request...\n";
        
        $courseForValidation = Course::findOrFail($courseId);
        $validation = $discountService->validateDiscountForCourse(
            $appliedDiscountFromRequest['code'], 
            $courseForValidation
        );
        
        if ($validation['valid']) {
            echo "   ✅ Discount validation passed\n";
            $transactionService->applyDiscount($validation['discount']);
            echo "   ✅ Discount re-applied to session\n";
            
            $sessionDiscountAfterReapply = session('applied_discount');
            echo "   📊 Session after re-apply: " . json_encode($sessionDiscountAfterReapply, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   ❌ Discount validation failed: {$validation['message']}\n";
        }
    }
    
    // Step 4: Call PaymentService (simulate createCoursePayment)
    echo "\n   💰 Calling PaymentService...\n";
    
    // Check session sebelum PaymentService
    echo "   📊 Session sebelum PaymentService:\n";
    echo "       - applied_discount: " . json_encode(session('applied_discount'), JSON_PRETTY_PRINT) . "\n";
    echo "       - course_id: " . session('course_id') . "\n";
    
    // Simulate PaymentService::createCoursePayment logic
    $appliedDiscountInPayment = session()->get('applied_discount');
    $discountAmountInPayment = 0;
    $discountIdInPayment = null;
    
    if ($appliedDiscountInPayment) {
        $discountIdInPayment = $appliedDiscountInPayment['id'] ?? null;
        
        if (isset($appliedDiscountInPayment['type']) && isset($appliedDiscountInPayment['value'])) {
            if ($appliedDiscountInPayment['type'] === 'percentage') {
                $discountAmountInPayment = ($course->price * $appliedDiscountInPayment['value']) / 100;
                if (isset($appliedDiscountInPayment['maximum_discount']) && $appliedDiscountInPayment['maximum_discount'] > 0) {
                    $discountAmountInPayment = min($discountAmountInPayment, $appliedDiscountInPayment['maximum_discount']);
                }
            } else {
                $discountAmountInPayment = min($appliedDiscountInPayment['value'], $course->price);
            }
        }
        
        echo "   ✅ Discount found in PaymentService\n";
        echo "   📊 Calculated discount amount: Rp " . number_format($discountAmountInPayment, 0, ',', '.') . "\n";
        echo "   📊 Discount ID: {$discountIdInPayment}\n";
    } else {
        echo "   ❌ NO DISCOUNT FOUND IN PAYMENT SERVICE!\n";
        echo "   🚨 MASALAH DITEMUKAN: Session discount hilang saat PaymentService\n";
    }
    
    // 4. ANALISIS PERBEDAAN
    echo "\n4. ANALISIS PERBEDAAN:\n";
    
    echo "   📊 PERBANDINGAN HASIL:\n";
    echo "   - Tes (berhasil): Discount amount = Rp " . number_format($discountAmount, 0, ',', '.') . ", ID = {$discountId}\n";
    echo "   - Real (gagal): Discount amount = Rp " . number_format($discountAmountInPayment, 0, ',', '.') . ", ID = {$discountIdInPayment}\n";
    
    if ($discountAmount != $discountAmountInPayment || $discountId != $discountIdInPayment) {
        echo "   🚨 PERBEDAAN DITEMUKAN!\n";
        
        if ($discountAmountInPayment == 0) {
            echo "   🔍 ROOT CAUSE: Session discount hilang di alur real\n";
            echo "   💡 KEMUNGKINAN PENYEBAB:\n";
            echo "       1. Session flush/clear di antara AJAX dan payment\n";
            echo "       2. Session ID berubah\n";
            echo "       3. Middleware yang menghapus session\n";
            echo "       4. Race condition antara AJAX dan payment request\n";
            echo "       5. Frontend tidak mengirim data discount dengan benar\n";
        }
    } else {
        echo "   ✅ Tidak ada perbedaan ditemukan dalam simulasi\n";
    }
    
    // 5. CEK TRANSAKSI REAL TERBARU
    echo "\n5. CEK TRANSAKSI REAL TERBARU:\n";
    
    $recentTransactions = Transaction::with(['student', 'course', 'discount'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "   📊 5 Transaksi terbaru:\n";
    foreach ($recentTransactions as $tx) {
        $discountInfo = $tx->discount ? "{$tx->discount->name} (Rp " . number_format($tx->discount_amount, 0, ',', '.') . ")" : "No discount";
        echo "   - {$tx->booking_trx_id}: {$tx->student->name} - {$discountInfo}\n";
        
        if ($tx->discount_amount == 0 && $tx->discount_id == null) {
            echo "     🚨 Transaksi ini tidak memiliki diskon meskipun mungkin seharusnya ada\n";
        }
    }
    
    // 6. REKOMENDASI PERBAIKAN
    echo "\n6. REKOMENDASI PERBAIKAN:\n";
    echo "   1. Tambahkan logging detail di FrontController::paymentStoreCoursesMidtrans\n";
    echo "   2. Tambahkan logging detail di PaymentService::createCoursePayment\n";
    echo "   3. Verifikasi session persistence antara AJAX dan payment request\n";
    echo "   4. Pastikan frontend mengirim data discount dengan benar\n";
    echo "   5. Cek apakah ada middleware yang mengganggu session\n";
    echo "   6. Implementasi fallback mechanism jika session discount hilang\n";
    
    echo "\n=== AUDIT SELESAI ===\n";
    
} catch (Exception $e) {
    echo "❌ Error during audit: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}