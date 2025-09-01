<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Discount;
use App\Services\TransactionService;
use App\Services\DiscountService;
use App\Http\Controllers\FrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

echo "=== TEST FRONTEND TO BACKEND TRANSMISSION ===\n\n";

// 1. Setup test data
echo "1. Setting up test data...\n";
$user = User::where('email', 'team@LMS.com')->first();
$course = Course::where('slug', 'modern-uiux-design-fundamentals')->first();
$discount = Discount::where('code', 'NEWYEAR2025')->first();

echo "   ✅ User: {$user->email}\n";
echo "   ✅ Course: {$course->name}\n";
echo "   ✅ Discount: {$discount->name} ({$discount->code})\n\n";

// 2. Clear session and login
Session::flush();
Auth::login($user);
echo "2. User logged in and session cleared\n\n";

// 3. Simulate user applying discount on checkout page
echo "3. Simulating user applying discount on checkout page...\n";
$transactionService = app(TransactionService::class);
$discountService = app(DiscountService::class);

// Prepare checkout (this sets course_id in session)
$checkoutData = $transactionService->prepareCourseCheckout($course);
echo "   ✅ Checkout prepared, course_id set in session\n";

// Apply discount
$validation = $discountService->validateDiscountForCourse($discount->code, $course);
if ($validation['valid']) {
    $transactionService->applyDiscount($validation['discount']);
    echo "   ✅ Discount applied to session\n";
}

// Check session state after discount application
echo "   📊 Session state after discount application:\n";
echo "       - course_id: " . session('course_id') . "\n";
echo "       - applied_discount: " . json_encode(session('applied_discount'), JSON_PRETTY_PRINT) . "\n\n";

// 4. Simulate frontend payment request (what JavaScript sends)
echo "4. Simulating frontend payment request...\n";

// This is what the JavaScript code sends in the request body
$frontendPayload = [
    'applied_discount' => [
        'id' => $discount->id,
        'code' => $discount->code,
        'name' => $discount->name,
        'type' => $discount->type,
        'value' => $discount->value
    ]
];

echo "   📊 Frontend payload (what JavaScript sends):\n";
echo "       " . json_encode($frontendPayload, JSON_PRETTY_PRINT) . "\n\n";

// 5. Test FrontController::paymentStoreCoursesMidtrans with this payload
echo "5. Testing FrontController::paymentStoreCoursesMidtrans...\n";

// Create request object with the frontend payload
$request = new Request();
$request->merge($frontendPayload);

// Enable detailed logging
Log::info('=== TESTING FRONTEND TO BACKEND TRANSMISSION ===');

// Create controller instance
$paymentService = app(\App\Services\PaymentService::class);
$courseService = app(\App\Services\CourseService::class);
$controller = new FrontController($paymentService, $transactionService, $courseService);

echo "   🔍 Calling paymentStoreCoursesMidtrans with frontend payload...\n";

try {
    // This should process the discount and create payment
    $response = $controller->paymentStoreCoursesMidtrans($request);
    
    echo "   ✅ Payment request processed successfully\n";
    echo "   📊 Response status: " . $response->getStatusCode() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if (isset($responseData['snap_token'])) {
        echo "   ✅ Snap token generated successfully\n";
        echo "   📊 Snap token length: " . strlen($responseData['snap_token']) . "\n";
    } else {
        echo "   ❌ No snap token in response\n";
        echo "   📊 Response data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Payment request failed: {$e->getMessage()}\n";
    echo "   📊 Error trace: {$e->getTraceAsString()}\n";
}

// 6. Check session state after payment request
echo "\n6. Checking session state after payment request...\n";
echo "   📊 Session state after payment request:\n";
echo "       - course_id: " . session('course_id') . "\n";
echo "       - applied_discount: " . json_encode(session('applied_discount'), JSON_PRETTY_PRINT) . "\n";

// 7. Check if PaymentService would receive correct data
echo "\n7. Testing PaymentService data reception...\n";

// Check what PaymentService::createCoursePayment would receive
$courseId = session('course_id');
if ($courseId) {
    echo "   ✅ Course ID available for PaymentService: {$courseId}\n";
    
    $sessionDiscount = session('applied_discount');
    if ($sessionDiscount) {
        echo "   ✅ Discount data available for PaymentService\n";
        echo "   📊 Discount data: " . json_encode($sessionDiscount, JSON_PRETTY_PRINT) . "\n";
        
        // Test if PaymentService would extract discount correctly
        $discountAmount = 0;
        $discountId = null;
        
        if ($sessionDiscount) {
            $discountModel = \App\Models\Discount::find($sessionDiscount['id']);
            if ($discountModel && $discountModel->isValid($course->price)) {
                $discountAmount = $discountModel->calculateDiscount($course->price);
                $discountId = $discountModel->id;
            }
        }
        
        echo "   📊 Calculated discount amount: {$discountAmount}\n";
        echo "   📊 Discount ID: {$discountId}\n";
        
        if ($discountAmount > 0 && $discountId) {
            echo "   ✅ PaymentService would receive correct discount data\n";
        } else {
            echo "   ❌ PaymentService would NOT receive correct discount data\n";
        }
    } else {
        echo "   ❌ No discount data available for PaymentService\n";
    }
} else {
    echo "   ❌ No course ID available for PaymentService\n";
}

echo "\n=== TEST COMPLETED ===\n";