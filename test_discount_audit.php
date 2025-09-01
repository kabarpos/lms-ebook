<?php

/**
 * AUDIT SISTEM DISKON - MENCARI AKAR MASALAH
 * 
 * Masalah: Besarnya diskon TIDAK TERCATAT di dashboard admin
 * Semua transaksi memiliki discount_amount = 0 dan discount_id = null
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Discount;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== AUDIT SISTEM DISKON ===\n\n";

// 1. Cek struktur database
echo "1. CHECKING DATABASE STRUCTURE:\n";
try {
    $columns = DB::select("DESCRIBE transactions");
    $discountFields = [];
    foreach ($columns as $column) {
        if (strpos($column->Field, 'discount') !== false) {
            $discountFields[] = $column->Field . ' (' . $column->Type . ')';
        }
    }
    echo "✓ Discount fields in transactions table: " . implode(', ', $discountFields) . "\n";
} catch (Exception $e) {
    echo "❌ Error checking database structure: " . $e->getMessage() . "\n";
}

// 2. Cek data diskon yang tersedia
echo "\n2. CHECKING AVAILABLE DISCOUNTS:\n";
$discounts = Discount::where('is_active', true)->get();
echo "✓ Active discounts found: " . $discounts->count() . "\n";
foreach ($discounts as $discount) {
    echo "  - {$discount->code}: {$discount->name} ({$discount->type} - {$discount->value})\n";
}

// 3. Cek transaksi dengan diskon
echo "\n3. CHECKING TRANSACTIONS WITH DISCOUNTS:\n";
$transactionsWithDiscount = Transaction::where('discount_amount', '>', 0)
    ->orWhereNotNull('discount_id')
    ->count();
echo "✓ Transactions with discount: {$transactionsWithDiscount}\n";

if ($transactionsWithDiscount > 0) {
    $sampleTransactions = Transaction::where('discount_amount', '>', 0)
        ->orWhereNotNull('discount_id')
        ->with('discount')
        ->limit(3)
        ->get();
    
    foreach ($sampleTransactions as $transaction) {
        echo "  - TRX {$transaction->booking_trx_id}: Discount Amount = {$transaction->discount_amount}, Discount ID = {$transaction->discount_id}\n";
    }
} else {
    echo "❌ NO TRANSACTIONS WITH DISCOUNT FOUND!\n";
}

// 4. Cek transaksi terbaru
echo "\n4. CHECKING RECENT TRANSACTIONS:\n";
$recentTransactions = Transaction::latest()->limit(5)->get();
foreach ($recentTransactions as $transaction) {
    echo "  - TRX {$transaction->booking_trx_id}: ";
    echo "Discount Amount = {$transaction->discount_amount}, ";
    echo "Discount ID = " . ($transaction->discount_id ?? 'null') . ", ";
    echo "Total = {$transaction->grand_total_amount}\n";
}

// 5. Simulasi alur data diskon
echo "\n5. SIMULATING DISCOUNT DATA FLOW:\n";

// Simulasi data dari session (applied_discount)
$appliedDiscount = [
    'id' => 1,
    'code' => 'TEST50',
    'name' => 'Test Discount 50%',
    'type' => 'percentage',
    'value' => 50,
    'maximum_discount' => 100000
];

// Simulasi perhitungan diskon
$coursePrice = 200000;
$adminFee = 5000;
$discountAmount = min(($coursePrice * $appliedDiscount['value'] / 100), $appliedDiscount['maximum_discount']);
$grandTotal = $coursePrice + $adminFee - $discountAmount;

echo "✓ Course Price: Rp " . number_format($coursePrice, 0, ',', '.') . "\n";
echo "✓ Admin Fee: Rp " . number_format($adminFee, 0, ',', '.') . "\n";
echo "✓ Discount Amount: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
echo "✓ Grand Total: Rp " . number_format($grandTotal, 0, ',', '.') . "\n";

// Simulasi custom_expiry yang dikirim ke Midtrans
$customExpiry = json_encode([
    'admin_fee_amount' => $adminFee,
    'discount_amount' => $discountAmount,
    'discount_id' => $appliedDiscount['id']
]);

echo "✓ Custom Expiry JSON: {$customExpiry}\n";

// Simulasi parsing di webhook
$parsedCustomExpiry = json_decode($customExpiry, true);
$webhookDiscountAmount = $parsedCustomExpiry['discount_amount'] ?? 0;
$webhookDiscountId = $parsedCustomExpiry['discount_id'] ?? null;

echo "✓ Webhook Parsed - Discount Amount: {$webhookDiscountAmount}\n";
echo "✓ Webhook Parsed - Discount ID: {$webhookDiscountId}\n";

// 6. Identifikasi masalah potensial
echo "\n6. POTENTIAL ISSUES ANALYSIS:\n";

$issues = [];

if ($transactionsWithDiscount == 0) {
    $issues[] = "❌ CRITICAL: No transactions with discount found in database";
}

if ($discounts->count() == 0) {
    $issues[] = "❌ WARNING: No active discounts available";
}

// Cek apakah ada session discount yang aktif
if (!session()->has('applied_discount')) {
    $issues[] = "❌ INFO: No active discount in current session";
}

// Cek log terbaru untuk webhook
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $hasWebhookLogs = strpos($logContent, 'custom_expiry') !== false;
    if (!$hasWebhookLogs) {
        $issues[] = "❌ WARNING: No webhook logs with custom_expiry found";
    }
} else {
    $issues[] = "❌ WARNING: Laravel log file not found";
}

if (empty($issues)) {
    echo "✅ No obvious issues detected\n";
} else {
    foreach ($issues as $issue) {
        echo "{$issue}\n";
    }
}

// 7. Rekomendasi
echo "\n7. RECOMMENDATIONS:\n";
echo "✓ Test checkout process with discount code\n";
echo "✓ Monitor webhook logs during discount transaction\n";
echo "✓ Verify custom_expiry data is sent to Midtrans\n";
echo "✓ Check if discount session data is properly passed to PaymentService\n";

echo "\n=== AUDIT COMPLETED ===\n";