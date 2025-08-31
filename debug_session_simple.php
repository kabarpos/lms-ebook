<?php

// Debug script sederhana untuk memverifikasi session state sistem diskon
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application dengan minimal setup
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== DEBUG SESSION STATE SISTEM DISKON ===\n\n";

// 1. Cek konfigurasi session
echo "1. KONFIGURASI SESSION:\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session Path: " . config('session.path') . "\n\n";

// 2. Test TransactionService prepareCourseCheckout
echo "2. TEST TRANSACTION SERVICE:\n";
try {
    $transactionService = app(App\Services\TransactionService::class);
    
    // Simulasi course_id untuk test
    $testCourseId = 1;
    
    echo "Testing prepareCourseCheckout dengan course_id: $testCourseId\n";
    
    // Bersihkan session terlebih dahulu
    session()->flush();
    echo "Session dibersihkan\n";
    
    $checkoutData = $transactionService->prepareCourseCheckout($testCourseId);
    
    echo "Applied Discount: " . (isset($checkoutData['appliedDiscount']) && $checkoutData['appliedDiscount'] ? json_encode($checkoutData['appliedDiscount']) : 'NULL') . "\n";
    echo "Discount Amount: " . ($checkoutData['discount_amount'] ?? 'NULL') . "\n";
    echo "Admin Fee: " . ($checkoutData['admin_fee_amount'] ?? 'NULL') . "\n";
    echo "Course ID: " . ($checkoutData['course_id'] ?? 'NULL') . "\n";
    
} catch (Exception $e) {
    echo "Error testing TransactionService: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// 3. Test DiscountService
echo "3. TEST DISCOUNT SERVICE:\n";
try {
    $discountService = app(App\Services\DiscountService::class);
    
    // Test dengan kode diskon yang tidak ada
    $testCode = 'NONEXISTENT';
    echo "Testing validateDiscount dengan kode: $testCode\n";
    
    $validation = $discountService->validateDiscount($testCode, 1);
    echo "Validation result: " . json_encode($validation) . "\n";
    
} catch (Exception $e) {
    echo "Error testing DiscountService: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Cek database diskon yang tersedia
echo "4. CEK DATABASE DISKON:\n";
try {
    $discounts = DB::table('discounts')
        ->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->select('code', 'name', 'type', 'value', 'minimum_purchase')
        ->limit(5)
        ->get();
    
    if ($discounts->count() > 0) {
        echo "Diskon aktif yang tersedia:\n";
        foreach ($discounts as $discount) {
            echo "- {$discount->code}: {$discount->name} ({$discount->type}: {$discount->value})\n";
        }
    } else {
        echo "Tidak ada diskon aktif yang tersedia\n";
    }
    
} catch (Exception $e) {
    echo "Error checking discounts: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== DEBUG SELESAI ===\n";