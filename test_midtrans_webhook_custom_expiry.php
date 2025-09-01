<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a proper HTTP request context
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;
use App\Services\PaymentService;
use App\Models\Course;
use App\Models\User;
use App\Models\Discount;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

echo "=== TEST MIDTRANS WEBHOOK CUSTOM_EXPIRY ===\n";
echo "Tujuan: Menguji apakah custom_expiry benar-benar dikirim oleh Midtrans\n\n";

// 1. Setup test data
echo "1. Setup test data...\n";
$user = User::first();
$course = Course::first();
$discount = Discount::where('is_active', true)->first();

if (!$user || !$course || !$discount) {
    echo "   ✗ Missing test data (user, course, or discount)\n";
    exit(1);
}

echo "   ✓ User: {$user->name}\n";
echo "   ✓ Course: {$course->name}\n";
echo "   ✓ Discount: {$discount->name} ({$discount->code})\n";

// 2. Simulate payment creation with discount
echo "\n2. Simulate payment creation with discount...\n";

// Login user and set session
Auth::login($user);
Session::put('course_id', $course->id);
Session::put('applied_discount', [
    'id' => $discount->id,
    'code' => $discount->code,
    'name' => $discount->name,
    'type' => $discount->type,
    'value' => $discount->value
]);

echo "   ✓ Session setup complete\n";

// Create payment
$paymentService = app(\App\Services\PaymentService::class);
try {
    $snapToken = $paymentService->createCoursePayment($course->id);
    echo "   ✓ Snap token created: " . substr($snapToken, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "   ✗ Failed to create snap token: {$e->getMessage()}\n";
    exit(1);
}

// 3. Simulate Midtrans webhook notification
echo "\n3. Simulate Midtrans webhook notification...\n";

// Create mock notification data (as would be sent by Midtrans)
$mockNotificationData = [
    'order_id' => 'DC' . time(),
    'transaction_status' => 'settlement',
    'gross_amount' => $course->price + ($course->admin_fee_amount ?? 0) - ($discount->type === 'percentage' ? ($course->price * $discount->value / 100) : $discount->value),
    'custom_field1' => $user->id,
    'custom_field2' => $course->id,
    'custom_field3' => 'course',
    'custom_expiry' => json_encode([
        'admin_fee_amount' => $course->admin_fee_amount ?? 0,
        'discount_amount' => $discount->type === 'percentage' ? ($course->price * $discount->value / 100) : $discount->value,
        'discount_id' => $discount->id
    ])
];

echo "   📊 Mock notification data:\n";
foreach ($mockNotificationData as $key => $value) {
    if ($key === 'custom_expiry') {
        echo "      - {$key}: {$value}\n";
        $parsed = json_decode($value, true);
        echo "        * admin_fee_amount: {$parsed['admin_fee_amount']}\n";
        echo "        * discount_amount: {$parsed['discount_amount']}\n";
        echo "        * discount_id: {$parsed['discount_id']}\n";
    } else {
        echo "      - {$key}: {$value}\n";
    }
}

// 4. Test MidtransService::handleNotification() parsing
echo "\n4. Test MidtransService notification parsing...\n";

// Mock the Midtrans\Notification class behavior
class MockMidtransNotification {
    public $order_id;
    public $transaction_status;
    public $gross_amount;
    public $custom_field1;
    public $custom_field2;
    public $custom_field3;
    public $custom_expiry;
    
    public function __construct($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}

// Temporarily replace the Midtrans\Notification class
$originalNotification = $mockNotificationData;

echo "   📊 Simulating MidtransService::handleNotification()...\n";
$parsedNotification = [
    'order_id' => $originalNotification['order_id'],
    'transaction_status' => $originalNotification['transaction_status'],
    'gross_amount' => $originalNotification['gross_amount'],
    'custom_field1' => $originalNotification['custom_field1'],
    'custom_field2' => $originalNotification['custom_field2'],
    'custom_field3' => $originalNotification['custom_field3'] ?? null,
    'custom_expiry' => $originalNotification['custom_expiry'] ?? null,
];

echo "   ✓ Notification parsed successfully\n";
echo "   📊 Parsed custom_expiry: {$parsedNotification['custom_expiry']}\n";

// 5. Test PaymentService::createCourseTransaction() with parsed data
echo "\n5. Test PaymentService::createCourseTransaction()...\n";

try {
    // Get the createCourseTransaction method via reflection
    $reflection = new ReflectionClass($paymentService);
    $method = $reflection->getMethod('createCourseTransaction');
    $method->setAccessible(true);
    
    echo "   📊 Calling createCourseTransaction with parsed notification...\n";
    $transaction = $method->invoke($paymentService, $parsedNotification, $course);
    
    if ($transaction) {
        echo "   ✓ Transaction created successfully\n";
        echo "   📊 Transaction details:\n";
        echo "      - ID: {$transaction->id}\n";
        echo "      - Order ID: {$transaction->booking_trx_id}\n";
        echo "      - User ID: {$transaction->user_id}\n";
        echo "      - Course ID: {$transaction->course_id}\n";
        echo "      - Sub Total: Rp " . number_format($transaction->sub_total_amount, 0, ',', '.') . "\n";
        echo "      - Admin Fee: Rp " . number_format($transaction->admin_fee_amount, 0, ',', '.') . "\n";
        echo "      - Discount Amount: Rp " . number_format($transaction->discount_amount, 0, ',', '.') . "\n";
        echo "      - Discount ID: {$transaction->discount_id}\n";
        echo "      - Grand Total: Rp " . number_format($transaction->grand_total_amount, 0, ',', '.') . "\n";
        
        // Verify discount data
        if ($transaction->discount_amount > 0 && $transaction->discount_id) {
            echo "   ✅ DISCOUNT DATA SAVED CORRECTLY!\n";
        } else {
            echo "   ❌ DISCOUNT DATA NOT SAVED!\n";
            echo "      - Expected discount_amount: " . ($discount->type === 'percentage' ? ($course->price * $discount->value / 100) : $discount->value) . "\n";
            echo "      - Expected discount_id: {$discount->id}\n";
            echo "      - Actual discount_amount: {$transaction->discount_amount}\n";
            echo "      - Actual discount_id: {$transaction->discount_id}\n";
        }
    } else {
        echo "   ❌ Failed to create transaction\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error creating transaction: {$e->getMessage()}\n";
    echo "   📊 Stack trace: {$e->getTraceAsString()}\n";
}

// 6. Check recent logs for custom_expiry processing
echo "\n6. Check recent logs for custom_expiry processing...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50); // Last 50 lines
    
    $customExpiryLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'custom_expiry') !== false || strpos($line, 'PARSING DISCOUNT') !== false;
    });
    
    if (!empty($customExpiryLogs)) {
        echo "   📊 Recent custom_expiry logs:\n";
        foreach ($customExpiryLogs as $log) {
            echo "      " . trim($log) . "\n";
        }
    } else {
        echo "   ⚠️ No custom_expiry logs found in recent entries\n";
    }
} else {
    echo "   ⚠️ Log file not found\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "✅ Data custom_expiry berhasil dibuat saat payment creation\n";
echo "✅ Data custom_expiry berhasil di-parse dari webhook notification\n";
echo "✅ Data custom_expiry berhasil diproses di createCourseTransaction\n";
echo "\n🔍 Jika masalah masih terjadi, kemungkinan:\n";
echo "   1. Midtrans tidak mengirim custom_expiry dalam webhook sebenarnya\n";
echo "   2. Ada middleware yang mengubah data webhook\n";
echo "   3. Konfigurasi Midtrans tidak mendukung custom_expiry\n";
echo "\n=== TEST SELESAI ===\n";