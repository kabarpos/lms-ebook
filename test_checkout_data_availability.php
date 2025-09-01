<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Discount;
use App\Services\TransactionService;
use App\Services\DiscountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "=== TEST CHECKOUT DATA AVAILABILITY ===\n\n";

// 1. Setup test data
echo "1. Setting up test data...\n";
$user = User::where('email', 'team@LMS.com')->first();
if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

$course = Course::where('slug', 'modern-uiux-design-fundamentals')->first();
if (!$course) {
    echo "❌ Course not found\n";
    exit(1);
}

$discount = Discount::where('code', 'NEWYEAR2025')->first();
if (!$discount) {
    echo "❌ Discount not found\n";
    exit(1);
}

echo "   ✅ User: {$user->email}\n";
echo "   ✅ Course: {$course->name}\n";
echo "   ✅ Discount: {$discount->name} ({$discount->code})\n\n";

// 2. Clear session and login
Session::flush();
Auth::login($user);
echo "2. User logged in and session cleared\n\n";

// 3. Apply discount to session
echo "3. Applying discount to session...\n";
$transactionService = app(TransactionService::class);
$discountService = app(DiscountService::class);

$validation = $discountService->validateDiscountForCourse($discount->code, $course);
if ($validation['valid']) {
    $transactionService->applyDiscount($validation['discount']);
    echo "   ✅ Discount applied to session\n";
} else {
    echo "   ❌ Discount validation failed: {$validation['message']}\n";
    exit(1);
}

// 4. Test prepareCourseCheckout method
echo "\n4. Testing prepareCourseCheckout method...\n";
$checkoutData = $transactionService->prepareCourseCheckout($course);

echo "   📊 Checkout data keys: " . implode(', ', array_keys($checkoutData)) . "\n";
echo "   📊 appliedDiscount in checkout data: " . (isset($checkoutData['appliedDiscount']) ? 'YES' : 'NO') . "\n";

if (isset($checkoutData['appliedDiscount'])) {
    echo "   📊 appliedDiscount data: " . json_encode($checkoutData['appliedDiscount'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "   ❌ appliedDiscount NOT found in checkout data\n";
}

// 5. Check session data
echo "\n5. Checking session data...\n";
$sessionDiscount = session('applied_discount');
if ($sessionDiscount) {
    echo "   ✅ Session discount found\n";
    echo "   📊 Session discount: " . json_encode($sessionDiscount, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "   ❌ No discount in session\n";
}

// 6. Simulate controller data preparation
echo "\n6. Simulating controller data preparation...\n";
$controllerData = array_merge($checkoutData, ['course' => $course]);

echo "   📊 Data yang akan dikirim ke view:\n";
echo "       - course: {$course->name}\n";
echo "       - sub_total_amount: {$checkoutData['sub_total_amount']}\n";
echo "       - discount_amount: {$checkoutData['discount_amount']}\n";
echo "       - grand_total_amount: {$checkoutData['grand_total_amount']}\n";
echo "       - appliedDiscount: " . (isset($checkoutData['appliedDiscount']) ? 'AVAILABLE' : 'NOT AVAILABLE') . "\n";

// 7. Test JavaScript initialization
echo "\n7. Testing JavaScript initialization...\n";
if (isset($checkoutData['appliedDiscount'])) {
    $jsCode = "let appliedDiscount = " . json_encode($checkoutData['appliedDiscount']) . ";";
    echo "   📊 JavaScript code that will be generated:\n";
    echo "       {$jsCode}\n";
    echo "   ✅ appliedDiscount will be properly initialized in JavaScript\n";
} else {
    echo "   📊 JavaScript code that will be generated:\n";
    echo "       let appliedDiscount = null;\n";
    echo "   ❌ appliedDiscount will be null in JavaScript\n";
}

echo "\n=== TEST COMPLETED ===\n";