<?php

// Debug script untuk memverifikasi session state sistem diskon
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request instance
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Start session
session_start();

echo "=== DEBUG SESSION STATE SISTEM DISKON ===\n\n";

// 1. Cek Laravel Session
echo "1. LARAVEL SESSION INFO:\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session ID: " . session()->getId() . "\n";
echo "Session Started: " . (session()->isStarted() ? 'Yes' : 'No') . "\n\n";

// 2. Cek semua data session
echo "2. SEMUA DATA SESSION:\n";
$allSessionData = session()->all();
if (empty($allSessionData)) {
    echo "Session kosong\n";
} else {
    foreach ($allSessionData as $key => $value) {
        echo "$key: " . json_encode($value) . "\n";
    }
}
echo "\n";

// 3. Cek kunci diskon spesifik
echo "3. KUNCI DISKON SPESIFIK:\n";
$discountKeys = ['applied_discount', 'discount_amount', 'course_id', 'admin_fee_amount'];
foreach ($discountKeys as $key) {
    $value = session()->get($key);
    echo "$key: " . ($value ? json_encode($value) : 'NULL') . "\n";
}
echo "\n";

// 4. Test pembersihan session diskon
echo "4. PEMBERSIHAN SESSION DISKON:\n";
session()->forget(['applied_discount', 'discount_amount', 'course_id', 'admin_fee_amount']);
echo "Data session diskon berhasil dibersihkan\n\n";

// 5. Verifikasi pembersihan
echo "5. VERIFIKASI PEMBERSIHAN:\n";
foreach ($discountKeys as $key) {
    $value = session()->get($key);
    echo "$key: " . ($value ? json_encode($value) : 'NULL (BERSIH)') . "\n";
}
echo "\n";

// 6. Test TransactionService prepareCourseCheckout
echo "6. TEST TRANSACTION SERVICE:\n";
try {
    $transactionService = app(App\Services\TransactionService::class);
    
    // Simulasi course_id untuk test
    $testCourseId = 1;
    
    echo "Testing prepareCourseCheckout dengan course_id: $testCourseId\n";
    $checkoutData = $transactionService->prepareCourseCheckout($testCourseId);
    
    echo "Applied Discount: " . ($checkoutData['appliedDiscount'] ?? 'NULL') . "\n";
    echo "Discount Amount: " . ($checkoutData['discount_amount'] ?? 'NULL') . "\n";
    echo "Admin Fee: " . ($checkoutData['admin_fee_amount'] ?? 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "Error testing TransactionService: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== DEBUG SELESAI ===\n";

// Terminate the kernel
$kernel->terminate($request, $response);