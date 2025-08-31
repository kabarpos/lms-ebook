<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Course;
use App\Services\TransactionService;
use App\Services\DiscountService;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$response = $kernel->handle($request);

echo "=== DEBUG DISCOUNT VALIDATION ===\n\n";

try {
    // Test 1: Check if course exists
    echo "1. TESTING COURSE EXISTENCE:\n";
    $course = Course::where('slug', 'laravel-untuk-pemula')->first();
    if ($course) {
        echo "✓ Course found: {$course->name} (ID: {$course->id})\n";
    } else {
        echo "✗ Course not found with slug 'laravel-untuk-pemula'\n";
        $courses = Course::limit(3)->get(['id', 'name', 'slug']);
        echo "Available courses:\n";
        foreach ($courses as $c) {
            echo "  - {$c->slug} ({$c->name})\n";
        }
    }
    echo "\n";
    
    // Test 2: Check discount service
    echo "2. TESTING DISCOUNT SERVICE:\n";
    $discountService = app(DiscountService::class);
    $discount = $discountService->findByCode('FLASH50');
    if ($discount) {
        echo "✓ Discount found: {$discount->name} (Code: {$discount->code})\n";
        echo "  - Type: {$discount->type}\n";
        echo "  - Value: {$discount->value}\n";
        echo "  - Active: " . ($discount->is_active ? 'Yes' : 'No') . "\n";
        echo "  - Valid from: {$discount->valid_from}\n";
        echo "  - Valid until: {$discount->valid_until}\n";
    } else {
        echo "✗ Discount 'FLASH50' not found\n";
        $discounts = \App\Models\Discount::active()->limit(3)->get(['code', 'name']);
        echo "Available active discounts:\n";
        foreach ($discounts as $d) {
            echo "  - {$d->code} ({$d->name})\n";
        }
    }
    echo "\n";
    
    // Test 3: Test validation logic
    if ($course && $discount) {
        echo "3. TESTING VALIDATION LOGIC:\n";
        $validation = $discountService->validateDiscountForCourse('FLASH50', $course);
        echo "Validation result: " . ($validation['valid'] ? 'VALID' : 'INVALID') . "\n";
        echo "Message: {$validation['message']}\n";
        if ($validation['valid']) {
            echo "Discount amount: {$validation['discount_amount']}\n";
            echo "Final price: {$validation['final_price']}\n";
        }
    }
    echo "\n";
    
    // Test 4: Check session functionality
    echo "4. TESTING SESSION:\n";
    session()->put('test_key', 'test_value');
    $testValue = session()->get('test_key');
    echo "Session test: " . ($testValue === 'test_value' ? 'WORKING' : 'FAILED') . "\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "=== DEBUG COMPLETED ===\n";

// Terminate the application
$kernel->terminate($request, $response);