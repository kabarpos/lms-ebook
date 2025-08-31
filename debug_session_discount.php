<?php

// Debug script untuk memverifikasi masalah session diskon
// Simulasi skenario bug yang dilaporkan user dengan nilai yang tepat

echo "=== DEBUG SESSION DISCOUNT BUG - SKENARIO REAL ===\n\n";

// Simulasi data course sesuai laporan user
$coursePrice = 326000;
$adminFee = 3000;
$discountPercentage = 50;
$discountMaxAmount = 100000;

// Simulasi session awal (kosong)
$session = [];
echo "1. Session awal (kosong):\n";
var_dump($session);
echo "\n";

// Simulasi apply diskon pertama kali
echo "2. Apply diskon pertama kali:\n";
$discountAmount = min(($coursePrice * $discountPercentage) / 100, $discountMaxAmount);
echo "Calculated discount: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
$session['applied_discount'] = [
    'id' => 1,
    'code' => 'FLASH50',
    'name' => 'Diskon Flash Sale',
    'type' => 'percentage',
    'value' => $discountPercentage
];
$session['discount_amount'] = $discountAmount;
echo "Grand Total pertama: Rp " . number_format($coursePrice + $adminFee - $discountAmount, 0, ',', '.') . " (BENAR: 229.000)\n";
var_dump($session);
echo "\n";

// Simulasi remove diskon (MASALAH: discount_amount tidak dibersihkan)
echo "3. Remove diskon (BUG: discount_amount masih ada):\n";
unset($session['applied_discount']); // Hanya menghapus applied_discount
// $session['discount_amount'] TIDAK DIHAPUS - INI MASALAHNYA!
echo "Session setelah remove (BUG - discount_amount masih ada):";
var_dump($session);
echo "Grand Total setelah remove: Rp " . number_format($coursePrice + $adminFee, 0, ',', '.') . " (BENAR: 329.000)\n";
echo "\n";

// Simulasi apply diskon kedua kali (MASALAH: discount_amount lama masih ada)
echo "4. Apply diskon kedua kali (BUG: discount_amount akumulasi):\n";
$newDiscountAmount = min(($coursePrice * $discountPercentage) / 100, $discountMaxAmount);
$session['applied_discount'] = [
    'id' => 1,
    'code' => 'FLASH50',
    'name' => 'Diskon Flash Sale',
    'type' => 'percentage',
    'value' => $discountPercentage
];

// MASALAH KRITIS: TransactionService::prepareCourseCheckout() menghitung discount_amount baru
// tapi PaymentService::createCoursePayment() menggunakan session['discount_amount'] yang lama!
echo "Old discount_amount dari session: Rp " . number_format($session['discount_amount'], 0, ',', '.') . "\n";
echo "New discount_amount yang dihitung TransactionService: Rp " . number_format($newDiscountAmount, 0, ',', '.') . "\n";

// TransactionService akan update session dengan nilai baru
$session['discount_amount'] = $newDiscountAmount; // Ini yang dilakukan prepareCourseCheckout
echo "Session discount_amount setelah prepareCourseCheckout: Rp " . number_format($session['discount_amount'], 0, ',', '.') . "\n";

// Tapi PaymentService mungkin menggunakan nilai yang salah atau ada race condition
// Mari simulasikan bug yang mungkin terjadi
echo "\n=== SIMULASI BUG YANG MUNGKIN TERJADI ===\n";

// Kemungkinan 1: PaymentService menggunakan session discount_amount yang tidak ter-update
$buggyDiscountAmount = 163000; // Nilai yang muncul di Midtrans menurut user
echo "Buggy discount amount yang muncul di Midtrans: Rp " . number_format($buggyDiscountAmount, 0, ',', '.') . "\n";
echo "Buggy Grand Total: Rp " . number_format($coursePrice + $adminFee - $buggyDiscountAmount, 0, ',', '.') . " (SALAH: 166.000)\n";

// Analisis: 163000 = 100000 (diskon pertama) + 63000 (diskon kedua yang terpotong?)
// Atau ada masalah lain dalam perhitungan
echo "\nAnalisis nilai 163.000:\n";
echo "- Diskon normal 50%: Rp " . number_format($newDiscountAmount, 0, ',', '.') . "\n";
echo "- Jika ada akumulasi: Rp " . number_format($newDiscountAmount + 63000, 0, ',', '.') . "\n";
echo "- Kemungkinan ada bug di perhitungan atau session management\n";

echo "\n=== SOLUSI YANG DIPERLUKAN ===\n";
echo "1. Pastikan TransactionService::removeDiscount() membersihkan SEMUA session diskon\n";
echo "2. PaymentService harus selalu menghitung ulang discount_amount dari applied_discount\n";
echo "3. Jangan bergantung pada session 'discount_amount' yang bisa tidak sinkron\n";
echo "4. Tambahkan logging untuk debug session state\n";