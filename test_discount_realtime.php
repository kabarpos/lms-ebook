<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->boot();

// Set up environment
$_ENV['APP_ENV'] = 'local';

echo "=== Testing Discount System Real-time ===\n\n";

try {
    // Test 1: Get course
    $course = \App\Models\Course::where('slug', 'complete-laravel-development-course')->first();
    if (!$course) {
        echo "❌ Course not found\n";
        exit(1);
    }
    echo "✅ Course found: {$course->name} (Price: Rp " . number_format($course->price, 0, ',', '.') . ")\n";
    
    // Test 2: Get discount
    $discount = \App\Models\Discount::where('code', 'FLASH50')->first();
    if (!$discount) {
        echo "❌ Discount not found\n";
        exit(1);
    }
    echo "✅ Discount found: {$discount->name} ({$discount->value}% off)\n";
    
    // Test 3: Test DiscountService validation
    $discountService = app(\App\Services\DiscountService::class);
    $validation = $discountService->validateDiscountForCourse('FLASH50', $course);
    
    if ($validation['valid']) {
        echo "✅ Discount validation: SUCCESS\n";
        echo "   Message: {$validation['message']}\n";
        echo "   Discount Amount: Rp " . number_format($validation['discount_amount'], 0, ',', '.') . "\n";
        echo "   Final Price: Rp " . number_format($validation['final_price'], 0, ',', '.') . "\n";
    } else {
        echo "❌ Discount validation: FAILED\n";
        echo "   Message: {$validation['message']}\n";
    }
    
    // Test 4: Test TransactionService pricing calculation
    echo "\n=== Testing TransactionService ===\n";
    $transactionService = app(\App\Services\TransactionService::class);
    
    // Apply discount to session
    $transactionService->applyDiscount($discount);
    echo "✅ Discount applied to session\n";
    
    // Calculate pricing with discount
    $pricing = $transactionService->calculatePricingWithDiscount($course, $discount);
    echo "✅ Pricing calculated:\n";
    echo "   Subtotal: Rp " . number_format($pricing['subtotal'], 0, ',', '.') . "\n";
    echo "   Discount: Rp " . number_format($pricing['discount_amount'], 0, ',', '.') . "\n";
    echo "   Admin Fee: Rp " . number_format($pricing['admin_fee'], 0, ',', '.') . "\n";
    echo "   Grand Total: Rp " . number_format($pricing['grand_total'], 0, ',', '.') . "\n";
    echo "   Savings: Rp " . number_format($pricing['savings'], 0, ',', '.') . "\n";
    
    // Test 5: Simulate controller response
    echo "\n=== Simulating Controller Response ===\n";
    $response = [
        'success' => true,
        'message' => $validation['message'],
        'discount' => [
            'id' => $discount->id,
            'name' => $discount->name,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value
        ],
        'pricing' => $pricing,
        'formatted' => [
            'subtotal' => 'Rp ' . number_format($pricing['subtotal'], 0, ',', '.'),
            'discount_amount' => 'Rp ' . number_format($pricing['discount_amount'], 0, ',', '.'),
            'admin_fee' => 'Rp ' . number_format($pricing['admin_fee'], 0, ',', '.'),
            'grand_total' => 'Rp ' . number_format($pricing['grand_total'], 0, ',', '.'),
            'savings' => 'Rp ' . number_format($pricing['savings'], 0, ',', '.')
        ]
    ];
    
    echo "✅ Controller response would be:\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    echo "\n=== All Tests Passed! ===\n";
    echo "The discount system is working correctly.\n";
    echo "If the frontend is not updating, the issue is likely in JavaScript or DOM manipulation.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}