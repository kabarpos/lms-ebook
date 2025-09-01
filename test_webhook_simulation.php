<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

echo "=== SIMULASI WEBHOOK MIDTRANS DENGAN DISKON ===\n\n";

// 1. Persiapan data test
echo "1. PERSIAPAN DATA TEST:\n";
$course = Course::first();
$user = User::first();

if (!$course || !$user) {
    echo "   ✗ Data course atau user tidak tersedia!\n";
    exit(1);
}

echo "   ✓ Course: {$course->name} (ID: {$course->id})\n";
echo "   ✓ User: {$user->name} (ID: {$user->id})\n\n";

// 2. Simulasi notifikasi webhook dengan diskon
echo "2. SIMULASI WEBHOOK NOTIFICATION:\n";

// Data webhook simulasi dengan custom_expiry yang berisi diskon
$webhookData = [
    'transaction_status' => 'settlement',
    'order_id' => 'TRX-TEST-' . time(),
    'gross_amount' => '296000.00', // 326000 - 30000 (diskon)
    'custom_field1' => $user->id,
    'custom_field2' => $course->id,
    'custom_field3' => 'course',
    'custom_expiry' => json_encode([
        'admin_fee_amount' => 0,
        'discount_amount' => 30000,
        'discount_id' => 4
    ])
];

echo "   Data webhook yang akan diproses:\n";
foreach ($webhookData as $key => $value) {
    if ($key === 'custom_expiry') {
        echo "   - {$key}: {$value}\n";
        $parsed = json_decode($value, true);
        echo "     * admin_fee_amount: {$parsed['admin_fee_amount']}\n";
        echo "     * discount_amount: {$parsed['discount_amount']}\n";
        echo "     * discount_id: {$parsed['discount_id']}\n";
    } else {
        echo "   - {$key}: {$value}\n";
    }
}

echo "\n3. SIMULASI PROSES createCourseTransaction:\n";

try {
    $paymentService = app(PaymentService::class);
    
    // Panggil method createCourseTransaction secara langsung
    $reflection = new ReflectionClass($paymentService);
    $method = $reflection->getMethod('createCourseTransaction');
    $method->setAccessible(true);
    
    echo "   Memanggil createCourseTransaction dengan data webhook...\n";
    $result = $method->invoke($paymentService, $webhookData, $course);
    
    if ($result) {
        echo "   ✓ Transaction berhasil dibuat dengan ID: {$result->id}\n";
        echo "   ✓ Discount amount: Rp " . number_format($result->discount_amount) . "\n";
        echo "   ✓ Discount ID: {$result->discount_id}\n";
        echo "   ✓ Grand total: Rp " . number_format($result->grand_total_amount) . "\n";
    } else {
        echo "   ✗ Gagal membuat transaction!\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// 4. Verifikasi data di database
echo "\n4. VERIFIKASI DATABASE:\n";

$latestTransaction = \App\Models\Transaction::latest()->first();
if ($latestTransaction) {
    echo "   Transaction terbaru:\n";
    echo "   - ID: {$latestTransaction->id}\n";
    echo "   - Course ID: {$latestTransaction->course_id}\n";
    echo "   - User ID: {$latestTransaction->user_id}\n";
    echo "   - Discount Amount: Rp " . number_format($latestTransaction->discount_amount) . "\n";
    echo "   - Discount ID: {$latestTransaction->discount_id}\n";
    echo "   - Grand Total: Rp " . number_format($latestTransaction->grand_total_amount) . "\n";
    echo "   - Created: {$latestTransaction->created_at}\n";
} else {
    echo "   ✗ Tidak ada transaction yang ditemukan\n";
}

// 5. Cek log untuk debugging
echo "\n5. CEK LOG TERBARU:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    
    // Cari log terkait transaction creation
    $transactionLogs = array_filter($lines, function($line) {
        return strpos($line, 'Creating course transaction') !== false ||
               strpos($line, 'Course transaction created') !== false ||
               strpos($line, 'custom_expiry') !== false;
    });
    
    if (!empty($transactionLogs)) {
        echo "   Log transaction creation ditemukan:\n";
        foreach (array_slice($transactionLogs, -5) as $log) {
            echo "   " . $log . "\n";
        }
    } else {
        echo "   Tidak ada log transaction creation ditemukan\n";
    }
} else {
    echo "   File log tidak ditemukan\n";
}

// 6. Test parsing custom_expiry
echo "\n6. TEST PARSING CUSTOM_EXPIRY:\n";
$customExpiry = $webhookData['custom_expiry'];
echo "   Raw custom_expiry: {$customExpiry}\n";

$parsed = json_decode($customExpiry, true);
if ($parsed) {
    echo "   ✓ JSON parsing berhasil:\n";
    echo "   - admin_fee_amount: {$parsed['admin_fee_amount']}\n";
    echo "   - discount_amount: {$parsed['discount_amount']}\n";
    echo "   - discount_id: {$parsed['discount_id']}\n";
} else {
    echo "   ✗ JSON parsing gagal!\n";
    echo "   JSON error: " . json_last_error_msg() . "\n";
}

echo "\n=== SIMULASI SELESAI ===\n";