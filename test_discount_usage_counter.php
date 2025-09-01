<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Course;
use App\Models\Discount;
use App\Models\User;
use App\Models\PaymentTemp;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;

echo "=== TEST DISCOUNT USAGE COUNTER ===\n\n";

// 1. Pilih discount yang akan ditest
$discount = Discount::where('code', 'SAPI50')->first();
if (!$discount) {
    echo "❌ Discount SAPI50 tidak ditemukan!\n";
    exit(1);
}

echo "1. DISCOUNT SEBELUM TEST:\n";
echo "   Code: {$discount->code}\n";
echo "   Used Count: {$discount->used_count}\n";
echo "   Usage Limit: {$discount->usage_limit}\n";
echo "   Available: " . ($discount->usage_limit - $discount->used_count) . "\n\n";

// 2. Pilih course untuk test
$course = Course::first();
if (!$course) {
    echo "❌ Tidak ada course yang tersedia!\n";
    exit(1);
}

echo "2. COURSE UNTUK TEST:\n";
echo "   ID: {$course->id}\n";
echo "   Name: {$course->name}\n";
echo "   Price: Rp " . number_format($course->price) . "\n\n";

// 3. Hitung discount amount
$discountAmount = $discount->calculateDiscount($course->price);
echo "3. DISCOUNT CALCULATION:\n";
echo "   Discount Amount: Rp " . number_format($discountAmount) . "\n";
echo "   Final Price: Rp " . number_format($course->price - $discountAmount) . "\n\n";

// 4. Buat PaymentTemp record untuk simulasi
$orderId = 'TEST-USAGE-' . time();
$adminFee = $course->admin_fee_amount ?? 0;
$grandTotal = $course->price - $discountAmount + $adminFee;

$paymentTemp = PaymentTemp::create([
    'order_id' => $orderId,
    'user_id' => 1, // Assume user ID 1 exists
    'course_id' => $course->id,
    'discount_id' => $discount->id,
    'discount_amount' => $discountAmount,
    'sub_total_amount' => $course->price,
    'admin_fee_amount' => $adminFee,
    'grand_total_amount' => $grandTotal,
    'expires_at' => now()->addHours(1)
]);

echo "4. PAYMENT TEMP CREATED:\n";
echo "   Order ID: {$paymentTemp->order_id}\n";
echo "   Discount ID: {$paymentTemp->discount_id}\n";
echo "   Discount Amount: Rp " . number_format($paymentTemp->discount_amount) . "\n\n";

// 5. Simulasi webhook notification dengan custom_expiry = null
$webhookData = [
    'order_id' => $orderId,
    'transaction_status' => 'settlement',
    'gross_amount' => $paymentTemp->grand_total_amount,
    'custom_field1' => $paymentTemp->user_id,
    'custom_field2' => $course->id,
    'custom_expiry' => null, // Simulasi masalah Midtrans
    'payment_type' => 'bank_transfer',
    'transaction_time' => now()->toISOString()
];

echo "5. WEBHOOK SIMULATION:\n";
echo "   Order ID: {$webhookData['order_id']}\n";
echo "   Status: {$webhookData['transaction_status']}\n";
echo "   Custom Expiry: " . ($webhookData['custom_expiry'] ?? 'null') . "\n\n";

// 6. Test PaymentService createCourseTransaction
echo "6. TESTING PAYMENT SERVICE...\n";

try {
    $paymentService = app(PaymentService::class);
    
    // Panggil method createCourseTransaction secara langsung
    $reflection = new ReflectionClass($paymentService);
    $method = $reflection->getMethod('createCourseTransaction');
    $method->setAccessible(true);
    
    echo "   Memanggil createCourseTransaction...\n";
    $transaction = $method->invoke($paymentService, $webhookData, $course);
    
    if ($transaction) {
        echo "   ✅ Transaction berhasil dibuat:\n";
        echo "      Transaction ID: {$transaction->id}\n";
        echo "      Booking TRX ID: {$transaction->booking_trx_id}\n";
        echo "      Discount ID: {$transaction->discount_id}\n";
        echo "      Discount Amount: Rp " . number_format($transaction->discount_amount) . "\n";
        echo "      Grand Total: Rp " . number_format($transaction->grand_total_amount) . "\n\n";
        
        // 7. Cek discount usage setelah transaksi
        $discountAfter = Discount::find($discount->id);
        echo "7. DISCOUNT SETELAH TRANSAKSI:\n";
        echo "   Code: {$discountAfter->code}\n";
        echo "   Used Count SEBELUM: {$discount->used_count}\n";
        echo "   Used Count SETELAH: {$discountAfter->used_count}\n";
        echo "   Usage Limit: {$discountAfter->usage_limit}\n";
        echo "   Available: " . ($discountAfter->usage_limit - $discountAfter->used_count) . "\n\n";
        
        // Verifikasi increment
        if ($discountAfter->used_count > $discount->used_count) {
            echo "   ✅ USAGE COUNTER BERHASIL BERTAMBAH!\n";
            echo "   ✅ Increment: +" . ($discountAfter->used_count - $discount->used_count) . "\n\n";
        } else {
            echo "   ❌ USAGE COUNTER TIDAK BERTAMBAH!\n";
            echo "   ❌ Masih sama: {$discountAfter->used_count}\n\n";
        }
        
        // 8. Cleanup test transaction
        echo "8. CLEANUP TEST DATA:\n";
        $transaction->delete();
        echo "   ✅ Test transaction deleted\n";
        
        // Reset discount counter untuk test berikutnya
        $discountAfter->decrement('used_count');
        echo "   ✅ Discount counter reset\n";
        
    } else {
        echo "   ❌ Gagal membuat transaction!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// 9. Verifikasi PaymentTemp cleanup
echo "\n9. VERIFIKASI PAYMENT TEMP CLEANUP:\n";
$remainingPaymentTemp = PaymentTemp::where('order_id', $orderId)->first();
if (!$remainingPaymentTemp) {
    echo "   ✅ PaymentTemp record berhasil dihapus\n";
} else {
    echo "   ❌ PaymentTemp record masih ada\n";
    // Cleanup manual
    $remainingPaymentTemp->delete();
    echo "   ✅ Manual cleanup completed\n";
}

echo "\n=== TEST SELESAI ===\n";