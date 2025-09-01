<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;
use App\Services\DiscountService;
use App\Models\Course;
use Carbon\Carbon;

echo "=== DEBUG DISCOUNT VALIDATION ===\n";
echo "Waktu sekarang: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

// Ambil semua diskon
$allDiscounts = Discount::all();
echo "Total diskon di database: " . $allDiscounts->count() . "\n\n";

// Periksa setiap diskon
foreach ($allDiscounts as $discount) {
    echo "=== DISKON: {$discount->code} ===\n";
    echo "Nama: {$discount->name}\n";
    echo "Tipe: {$discount->type}\n";
    echo "Value: {$discount->value}\n";
    echo "Is Active: " . ($discount->is_active ? 'Ya' : 'Tidak') . "\n";
    echo "Start Date: " . ($discount->start_date ? $discount->start_date->format('Y-m-d H:i:s') : 'Tidak ada') . "\n";
    echo "End Date: " . ($discount->end_date ? $discount->end_date->format('Y-m-d H:i:s') : 'Tidak ada') . "\n";
    echo "Usage Limit: " . ($discount->usage_limit ?? 'Unlimited') . "\n";
    echo "Used Count: {$discount->used_count}\n";
    echo "Minimum Amount: " . ($discount->minimum_amount ?? 'Tidak ada') . "\n";
    echo "Maximum Discount: " . ($discount->maximum_discount ?? 'Tidak ada') . "\n";
    
    // Test validasi dengan berbagai skenario
    echo "\n--- VALIDASI ---\n";
    
    // Test dengan amount 0
    $isValid0 = $discount->isValid(0);
    echo "Valid untuk amount 0: " . ($isValid0 ? 'Ya' : 'Tidak') . "\n";
    
    // Test dengan amount 100000
    $isValid100k = $discount->isValid(100000);
    echo "Valid untuk amount 100k: " . ($isValid100k ? 'Ya' : 'Tidak') . "\n";
    
    // Test dengan amount 500000
    $isValid500k = $discount->isValid(500000);
    echo "Valid untuk amount 500k: " . ($isValid500k ? 'Ya' : 'Tidak') . "\n";
    
    // Cek mengapa tidak valid jika tidak valid
    if (!$isValid100k) {
        echo "Alasan tidak valid untuk 100k:\n";
        if (!$discount->is_active) {
            echo "- Tidak aktif\n";
        }
        if ($discount->start_date && Carbon::now()->lt($discount->start_date)) {
            echo "- Belum dimulai (start: {$discount->start_date})\n";
        }
        if ($discount->end_date && Carbon::now()->gt($discount->end_date)) {
            echo "- Sudah berakhir (end: {$discount->end_date})\n";
        }
        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            echo "- Kuota habis ({$discount->used_count}/{$discount->usage_limit})\n";
        }
        if ($discount->minimum_amount && 100000 < $discount->minimum_amount) {
            echo "- Minimum amount tidak terpenuhi (min: {$discount->minimum_amount})\n";
        }
    }
    
    echo "\n--- QUERY SCOPE TEST ---\n";
    
    // Test scope active
    $activeQuery = Discount::active()->where('code', $discount->code)->first();
    echo "Ditemukan di scope active(): " . ($activeQuery ? 'Ya' : 'Tidak') . "\n";
    
    // Test scope available
    $availableQuery = Discount::available()->where('code', $discount->code)->first();
    echo "Ditemukan di scope available(): " . ($availableQuery ? 'Ya' : 'Tidak') . "\n";
    
    // Test kombinasi active + available
    $combinedQuery = Discount::active()->available()->where('code', $discount->code)->first();
    echo "Ditemukan di scope active() + available(): " . ($combinedQuery ? 'Ya' : 'Tidak') . "\n";
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Test dengan DiscountService
echo "=== TEST DISCOUNT SERVICE ===\n";
$discountService = new DiscountService();

// Ambil course pertama untuk test
$course = Course::first();
if ($course) {
    echo "Testing dengan course: {$course->name} (Price: {$course->price})\n\n";
    
    foreach ($allDiscounts as $discount) {
        echo "Testing code: {$discount->code}\n";
        $validation = $discountService->validateDiscountForCourse($discount->code, $course);
        echo "Valid: " . ($validation['valid'] ? 'Ya' : 'Tidak') . "\n";
        echo "Message: {$validation['message']}\n";
        if ($validation['valid']) {
            echo "Discount Amount: {$validation['discount_amount']}\n";
            echo "Final Price: {$validation['final_price']}\n";
        }
        echo "\n";
    }
} else {
    echo "Tidak ada course untuk testing\n";
}

echo "=== SELESAI ===\n";