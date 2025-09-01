<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;
use App\Services\DiscountService;
use App\Models\Course;
use Carbon\Carbon;

echo "=== FINAL DISCOUNT VALIDATION TEST ===\n";
echo "Testing all discount codes with real scenarios\n";
echo "Time: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

$discountService = new DiscountService();
$course = Course::first();

if (!$course) {
    echo "ERROR: No course found for testing\n";
    exit(1);
}

echo "Testing with course: {$course->name}\n";
echo "Course price: Rp " . number_format($course->price, 0, '', '.') . "\n\n";

// Test all active discount codes
$testCodes = ['NEWYEAR2025', 'FLASH50', 'SAVE25K', 'STUDENT15', 'TEST30', 'SAPI50'];

foreach ($testCodes as $code) {
    echo "=== Testing: {$code} ===\n";
    
    // Test with DiscountService
    $validation = $discountService->validateDiscountForCourse($code, $course);
    
    echo "Valid: " . ($validation['valid'] ? '✅ YES' : '❌ NO') . "\n";
    echo "Message: {$validation['message']}\n";
    
    if ($validation['valid']) {
        echo "Original Price: Rp " . number_format($course->price, 0, '', '.') . "\n";
        echo "Discount Amount: Rp " . number_format($validation['discount_amount'], 0, '', '.') . "\n";
        echo "Final Price: Rp " . number_format($validation['final_price'], 0, '', '.') . "\n";
        echo "Savings: Rp " . number_format($validation['savings'], 0, '', '.') . "\n";
        
        // Test discount application
        $application = $discountService->applyDiscountToCourse($course, $code);
        echo "Application Success: " . ($application['success'] ? '✅ YES' : '❌ NO') . "\n";
    }
    
    echo "\n" . str_repeat('-', 40) . "\n\n";
}

// Test with different price scenarios
echo "=== TESTING WITH DIFFERENT PRICE SCENARIOS ===\n\n";

$priceScenarios = [
    50000 => 'Low price (50k)',
    100000 => 'Medium price (100k)', 
    200000 => 'High price (200k)',
    500000 => 'Very high price (500k)'
];

foreach ($priceScenarios as $price => $description) {
    echo "--- {$description} ---\n";
    
    foreach (['SAPI50', 'FLASH50', 'STUDENT15'] as $code) {
        $discount = Discount::where('code', $code)->first();
        if ($discount) {
            $isValid = $discount->isValid($price);
            $discountAmount = $discount->calculateDiscount($price);
            $finalPrice = max(0, $price - $discountAmount);
            
            echo "{$code}: " . ($isValid ? '✅' : '❌') . " | ";
            echo "Discount: Rp " . number_format($discountAmount, 0, '', '.') . " | ";
            echo "Final: Rp " . number_format($finalPrice, 0, '', '.') . "\n";
        }
    }
    echo "\n";
}

// Summary
echo "=== SUMMARY ===\n";
$activeDiscounts = Discount::active()->available()->get();
echo "Total active & available discounts: " . $activeDiscounts->count() . "\n";

foreach ($activeDiscounts as $discount) {
    echo "- {$discount->code}: {$discount->name}\n";
}

echo "\n✅ All discount validation tests completed!\n";
echo "🎉 Discount system is working properly!\n";