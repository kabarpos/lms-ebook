<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\Discount;
use App\Services\TransactionService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

echo "=== AUDIT ALUR CHECKOUT DENGAN DISKON ===\n\n";

// 1. Cek course yang tersedia
echo "1. COURSE YANG TERSEDIA:\n";
$courses = Course::take(3)->get(['id', 'name', 'price']);
foreach ($courses as $course) {
    echo "   - ID: {$course->id}, Name: {$course->name}, Price: Rp " . number_format($course->price) . "\n";
}

if ($courses->isEmpty()) {
    echo "   Tidak ada course yang tersedia!\n";
    exit(1);
}

$testCourse = $courses->first();
echo "\n   Course untuk testing: {$testCourse->name} (ID: {$testCourse->id})\n\n";

// 2. Cek diskon yang tersedia
echo "2. DISKON YANG TERSEDIA:\n";
$discounts = Discount::where('is_active', true)
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->get(['id', 'name', 'code', 'type', 'value', 'maximum_discount']);

foreach ($discounts as $discount) {
    echo "   - ID: {$discount->id}, Code: {$discount->code}, Name: {$discount->name}\n";
    echo "     Type: {$discount->type}, Value: {$discount->value}";
    if ($discount->maximum_discount) {
        echo ", Max: Rp " . number_format($discount->maximum_discount);
    }
    echo "\n";
}

if ($discounts->isEmpty()) {
    echo "   Tidak ada diskon aktif yang tersedia!\n";
    exit(1);
}

$testDiscount = $discounts->first();
echo "\n   Diskon untuk testing: {$testDiscount->code}\n\n";

// 3. Simulasi session checkout dengan diskon
echo "3. SIMULASI SESSION CHECKOUT:\n";

// Set course_id di session (seperti saat user masuk ke halaman checkout)
Session::put('course_id', $testCourse->id);
echo "   ✓ Course ID disimpan di session: " . Session::get('course_id') . "\n";

// Simulasi penerapan diskon (seperti saat user input kode diskon)
$transactionService = app(TransactionService::class);
$transactionService->applyDiscount($testDiscount);

$appliedDiscount = Session::get('applied_discount');
if ($appliedDiscount) {
    echo "   ✓ Diskon diterapkan di session:\n";
    echo "     - ID: {$appliedDiscount['id']}\n";
    echo "     - Code: {$appliedDiscount['code']}\n";
    echo "     - Name: {$appliedDiscount['name']}\n";
    echo "     - Type: {$appliedDiscount['type']}\n";
    echo "     - Value: {$appliedDiscount['value']}\n";
} else {
    echo "   ✗ Diskon TIDAK tersimpan di session!\n";
}

// 4. Simulasi proses payment (seperti saat user klik tombol bayar)
echo "\n4. SIMULASI PROSES PAYMENT:\n";

try {
    $paymentService = app(PaymentService::class);
    
    // Simulasi user login (diperlukan untuk PaymentService)
    $user = \App\Models\User::first();
    if (!$user) {
        echo "   ✗ Tidak ada user untuk testing!\n";
        exit(1);
    }
    Auth::login($user);
    echo "   ✓ User login simulasi: {$user->name}\n";
    
    // Cek session sebelum createCoursePayment
    echo "\n   Session sebelum createCoursePayment:\n";
    echo "   - course_id: " . Session::get('course_id', 'tidak ada') . "\n";
    echo "   - applied_discount: " . (Session::get('applied_discount') ? 'ada' : 'tidak ada') . "\n";
    
    if (Session::get('applied_discount')) {
        $sessionDiscount = Session::get('applied_discount');
        echo "     * Discount ID: {$sessionDiscount['id']}\n";
        echo "     * Discount Code: {$sessionDiscount['code']}\n";
    }
    
    // Panggil createCoursePayment (ini yang dipanggil saat user klik bayar)
    echo "\n   Memanggil PaymentService::createCoursePayment()...\n";
    $snapToken = $paymentService->createCoursePayment($testCourse->id);
    
    if ($snapToken) {
        echo "   ✓ Snap token berhasil dibuat: " . substr($snapToken, 0, 20) . "...\n";
    } else {
        echo "   ✗ Gagal membuat snap token!\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error saat proses payment: " . $e->getMessage() . "\n";
}

// 5. Cek log untuk melihat data yang dikirim ke Midtrans
echo "\n5. CEK LOG DISCOUNT CALCULATION:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $discountLogs = array_filter($lines, function($line) {
        return strpos($line, 'PaymentService discount calculation') !== false;
    });
    
    if (!empty($discountLogs)) {
        echo "   Log discount calculation ditemukan:\n";
        foreach (array_slice($discountLogs, -3) as $log) {
            echo "   " . $log . "\n";
        }
    } else {
        echo "   Tidak ada log 'PaymentService discount calculation' ditemukan\n";
    }
} else {
    echo "   File log tidak ditemukan\n";
}

// 6. Analisis masalah potensial
echo "\n6. ANALISIS MASALAH POTENSIAL:\n";

$issues = [];

// Cek apakah session masih ada setelah proses payment
if (!Session::get('applied_discount')) {
    $issues[] = "Session 'applied_discount' hilang setelah proses payment";
}

if (!Session::get('course_id')) {
    $issues[] = "Session 'course_id' hilang setelah proses payment";
}

// Cek apakah ada transaksi dengan diskon di database
$transactionsWithDiscount = \App\Models\Transaction::where('discount_amount', '>', 0)->count();
if ($transactionsWithDiscount == 0) {
    $issues[] = "Tidak ada transaksi dengan diskon yang tersimpan di database";
}

if (empty($issues)) {
    echo "   ✓ Tidak ada masalah yang terdeteksi\n";
} else {
    echo "   Masalah yang terdeteksi:\n";
    foreach ($issues as $issue) {
        echo "   ✗ {$issue}\n";
    }
}

// 7. Rekomendasi
echo "\n7. REKOMENDASI:\n";
echo "   1. Pastikan session tidak hilang saat proses checkout\n";
echo "   2. Tambahkan logging lebih detail di PaymentService::createCoursePayment\n";
echo "   3. Verifikasi bahwa custom_expiry benar-benar dikirim ke Midtrans\n";
echo "   4. Test manual dengan browser untuk memastikan session persist\n";
echo "   5. Cek apakah ada middleware yang menghapus session\n";

echo "\n=== AUDIT SELESAI ===\n";