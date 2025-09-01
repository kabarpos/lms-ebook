<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

echo "=== DEBUG MASALAH CHECKOUT REAL ===\n\n";

// 1. Analisis webhook real yang gagal
echo "1. ANALISIS WEBHOOK REAL YANG GAGAL:\n";

// Cari transaksi real yang memiliki custom_expiry null dari log
$failedOrders = ['DC8797', 'DC5705']; // Order ID dari log

foreach ($failedOrders as $orderId) {
    echo "\n   Order ID: {$orderId}\n";
    
    // Cari transaksi di database
    $transaction = Transaction::where('booking_trx_id', $orderId)->first();
    if ($transaction) {
        echo "   ✓ Transaksi ditemukan di database:\n";
        echo "     - ID: {$transaction->id}\n";
        echo "     - User ID: {$transaction->user_id}\n";
        echo "     - Course ID: {$transaction->course_id}\n";
        echo "     - Discount Amount: Rp " . number_format($transaction->discount_amount) . "\n";
        echo "     - Discount ID: {$transaction->discount_id}\n";
        echo "     - Grand Total: Rp " . number_format($transaction->grand_total_amount) . "\n";
        echo "     - Created: {$transaction->created_at}\n";
        
        // Cek user dan course
        $user = User::find($transaction->user_id);
        $course = Course::find($transaction->course_id);
        
        if ($user && $course) {
            echo "     - User: {$user->name} ({$user->email})\n";
            echo "     - Course: {$course->name} (Rp " . number_format($course->price) . ")\n";
            
            // Analisis apakah seharusnya ada diskon
            $expectedTotal = $course->price + ($course->admin_fee_amount ?? 0);
            $actualTotal = $transaction->grand_total_amount;
            
            if ($actualTotal < $expectedTotal) {
                $impliedDiscount = $expectedTotal - $actualTotal;
                echo "     ⚠️  KEMUNGKINAN ADA DISKON YANG TIDAK TERCATAT:\n";
                echo "       - Expected total: Rp " . number_format($expectedTotal) . "\n";
                echo "       - Actual total: Rp " . number_format($actualTotal) . "\n";
                echo "       - Implied discount: Rp " . number_format($impliedDiscount) . "\n";
            } else {
                echo "     ✓ Total sesuai dengan harga course (tidak ada diskon)\n";
            }
        }
    } else {
        echo "   ✗ Transaksi tidak ditemukan di database\n";
    }
}

// 2. Analisis pola masalah
echo "\n\n2. ANALISIS POLA MASALAH:\n";

// Cek semua transaksi dalam 24 jam terakhir
$recentTransactions = Transaction::where('created_at', '>=', now()->subDay())
    ->with(['user', 'course'])
    ->orderBy('created_at', 'desc')
    ->get();

echo "   Transaksi dalam 24 jam terakhir: {$recentTransactions->count()}\n";

$withDiscount = $recentTransactions->where('discount_amount', '>', 0)->count();
$withoutDiscount = $recentTransactions->where('discount_amount', '=', 0)->count();

echo "   - Dengan diskon: {$withDiscount}\n";
echo "   - Tanpa diskon: {$withoutDiscount}\n";

if ($withDiscount == 0 && $withoutDiscount > 0) {
    echo "   ⚠️  MASALAH: Semua transaksi tidak memiliki diskon!\n";
}

// 3. Cek apakah ada diskon aktif yang seharusnya bisa digunakan
echo "\n3. CEK DISKON AKTIF:\n";

$activeDiscounts = \App\Models\Discount::where('is_active', true)
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->get();

echo "   Diskon aktif: {$activeDiscounts->count()}\n";

foreach ($activeDiscounts as $discount) {
    echo "   - {$discount->code}: {$discount->name}\n";
    echo "     Type: {$discount->type}, Value: {$discount->value}\n";
    
    // Cek apakah diskon ini pernah digunakan
    $usageCount = Transaction::where('discount_id', $discount->id)->count();
    echo "     Usage count: {$usageCount}\n";
    
    if ($usageCount == 0) {
        echo "     ⚠️  Diskon ini belum pernah digunakan!\n";
    }
}

// 4. Analisis kemungkinan penyebab
echo "\n4. KEMUNGKINAN PENYEBAB MASALAH:\n";

$possibleCauses = [];

// Cek apakah ada middleware yang menghapus session
if (file_exists(app_path('Http/Middleware'))) {
    $middlewareFiles = glob(app_path('Http/Middleware') . '/*.php');
    foreach ($middlewareFiles as $file) {
        $content = file_get_contents($file);
        if (strpos($content, 'session()->forget') !== false || 
            strpos($content, 'session()->flush') !== false) {
            $possibleCauses[] = "Middleware " . basename($file) . " mungkin menghapus session";
        }
    }
}

// Cek apakah ada route yang menghapus session
if (file_exists(base_path('routes/web.php'))) {
    $routeContent = file_get_contents(base_path('routes/web.php'));
    if (strpos($routeContent, 'session()->forget') !== false) {
        $possibleCauses[] = "Ada route yang menghapus session";
    }
}

// Cek konfigurasi session
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
echo "   Session driver: {$sessionDriver}\n";
echo "   Session lifetime: {$sessionLifetime} minutes\n";

if ($sessionLifetime < 60) {
    $possibleCauses[] = "Session lifetime terlalu pendek ({$sessionLifetime} menit)";
}

// Cek apakah PaymentService benar-benar dipanggil dengan session yang benar
echo "\n   Kemungkinan penyebab:\n";
if (empty($possibleCauses)) {
    echo "   1. User tidak menerapkan diskon saat checkout\n";
    echo "   2. Session diskon hilang antara halaman checkout dan proses payment\n";
    echo "   3. Ada bug di frontend yang tidak mengirim data diskon\n";
    echo "   4. PaymentService dipanggil tanpa session yang benar\n";
} else {
    foreach ($possibleCauses as $i => $cause) {
        echo "   " . ($i + 1) . ". {$cause}\n";
    }
}

// 5. Rekomendasi debugging
echo "\n5. REKOMENDASI DEBUGGING:\n";
echo "   1. Tambahkan logging di FrontController::paymentStoreCoursesMidtrans untuk cek session\n";
echo "   2. Tambahkan logging di PaymentService::createCoursePayment untuk cek applied_discount\n";
echo "   3. Test manual dengan browser: apply diskon -> langsung bayar\n";
echo "   4. Cek apakah ada AJAX call yang menghapus session\n";
echo "   5. Monitor session storage di browser developer tools\n";
echo "   6. Tambahkan alert/console.log di frontend untuk debug\n";

// 6. Generate script untuk fix logging
echo "\n6. GENERATE ENHANCED LOGGING:\n";
echo "   Script untuk menambahkan logging akan dibuat...\n";

$loggingCode = '
// Tambahkan di FrontController::paymentStoreCoursesMidtrans()
Log::info("Payment request received", [
    "course_id" => session()->get("course_id"),
    "applied_discount" => session()->get("applied_discount"),
    "session_id" => session()->getId(),
    "user_id" => Auth::id()
]);

// Tambahkan di PaymentService::createCoursePayment() sebelum createSnapToken
Log::info("Creating payment with session data", [
    "course_id" => $courseId,
    "applied_discount" => session()->get("applied_discount"),
    "calculated_discount_amount" => $discountAmount,
    "custom_expiry_data" => [
        "admin_fee_amount" => $adminFeeAmount,
        "discount_amount" => $discountAmount,
        "discount_id" => $discountId
    ]
]);
';

file_put_contents('enhanced_logging_code.txt', $loggingCode);
echo "   ✓ Enhanced logging code disimpan di enhanced_logging_code.txt\n";

echo "\n=== DEBUG SELESAI ===\n";
echo "\nKESIMPULAN:\n";
echo "- Sistem diskon berfungsi dengan baik secara teknis\n";
echo "- Masalah utama: custom_expiry null di webhook real\n";
echo "- Kemungkinan besar: session diskon hilang atau tidak diterapkan saat checkout\n";
echo "- Solusi: Enhanced logging + test manual untuk identifikasi titik kegagalan\n";