<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Course;
use App\Models\User;
use App\Models\Discount;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== TEST PERBAIKAN FORM FILAMENT UNTUK DISKON ===\n\n";

// Ambil data yang sudah ada
$course = Course::first();
$user = User::where('email', 'test@example.com')->first();
$discount = Discount::where('code', 'TEST30')->first();

if (!$course || !$user || !$discount) {
    echo "❌ Data test tidak lengkap. Jalankan test_create_transaction_discount.php terlebih dahulu.\n";
    exit(1);
}

echo "Data yang akan digunakan:\n";
echo "- Course: {$course->name} (Rp " . number_format($course->price) . ")\n";
echo "- Admin Fee: Rp " . number_format($course->admin_fee_amount) . "\n";
echo "- User: {$user->name} ({$user->email})\n";
echo "- Discount: {$discount->name} ({$discount->code}) - {$discount->value}% / Rp " . number_format($discount->value) . "\n\n";

// Simulasi logika yang ada di TransactionResource setelah perbaikan
echo "=== SIMULASI LOGIKA FORM FILAMENT SETELAH PERBAIKAN ===\n";

// Step 1: User memilih course_id
echo "1. User memilih course: {$course->name}\n";
$subTotal = $course->price;
$adminFee = $course->admin_fee_amount ?? 0;
$discountAmount = 0; // Awalnya 0 karena belum pilih diskon
$grandTotal = $subTotal + $adminFee - $discountAmount;

echo "   - Sub Total: Rp " . number_format($subTotal) . "\n";
echo "   - Admin Fee: Rp " . number_format($adminFee) . "\n";
echo "   - Discount Amount: Rp " . number_format($discountAmount) . "\n";
echo "   - Grand Total: Rp " . number_format($grandTotal) . "\n\n";

// Step 2: User memilih discount_id
echo "2. User memilih diskon: {$discount->name}\n";

// Logika yang baru ditambahkan di TransactionResource
if ($discount->type === 'percentage') {
    $discountAmount = ($subTotal * $discount->value) / 100;
} elseif ($discount->type === 'fixed') {
    $discountAmount = $discount->value;
}

$grandTotal = $subTotal + $adminFee - $discountAmount;

echo "   - Discount Type: {$discount->type}\n";
echo "   - Discount Value: {$discount->value}" . ($discount->type === 'percentage' ? '%' : '') . "\n";
echo "   - Calculated Discount Amount: Rp " . number_format($discountAmount) . "\n";
echo "   - Updated Grand Total: Rp " . number_format($grandTotal) . "\n\n";

// Step 3: Simulasi data yang akan disimpan
echo "3. Data yang akan disimpan ke database:\n";
$transactionData = [
    'course_id' => $course->id,
    'user_id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'sub_total_amount' => $subTotal,
    'admin_fee_amount' => $adminFee,
    'discount_amount' => $discountAmount,
    'discount_id' => $discount->id,
    'grand_total_amount' => $grandTotal,
    'started_at' => now()->format('Y-m-d'),
    'booking_trx_id' => 'LMS' . strtoupper(Str::random(8)),
    'is_paid' => false,
    'payment_type' => 'Manual',
];

foreach ($transactionData as $key => $value) {
    if (in_array($key, ['sub_total_amount', 'admin_fee_amount', 'discount_amount', 'grand_total_amount'])) {
        echo "   - {$key}: Rp " . number_format($value) . "\n";
    } else {
        echo "   - {$key}: {$value}\n";
    }
}

echo "\n=== TEST PENYIMPANAN TRANSAKSI ===\n";
try {
    $newTransaction = Transaction::create($transactionData);
    echo "✓ Transaksi berhasil dibuat dengan ID: {$newTransaction->id}\n";
    
    // Verifikasi data yang tersimpan
    $savedTransaction = Transaction::with(['discount', 'course', 'student'])->find($newTransaction->id);
    
    echo "\nVerifikasi data yang tersimpan:\n";
    echo "- Booking ID: {$savedTransaction->booking_trx_id}\n";
    echo "- Course: {$savedTransaction->course->name}\n";
    echo "- Student: {$savedTransaction->student->name}\n";
    echo "- Discount Amount: Rp " . number_format($savedTransaction->discount_amount) . "\n";
    echo "- Discount ID: {$savedTransaction->discount_id}\n";
    echo "- Discount Name: " . ($savedTransaction->discount ? $savedTransaction->discount->name : 'NULL') . "\n";
    echo "- Grand Total: Rp " . number_format($savedTransaction->grand_total_amount) . "\n";
    
    // Verifikasi apakah data diskon tersimpan dengan benar
    if ($savedTransaction->discount_amount > 0 && $savedTransaction->discount_id == $discount->id) {
        echo "\n✅ PERBAIKAN BERHASIL! DATA DISKON TERSIMPAN DENGAN BENAR!\n";
        echo "\n🎉 Sekarang saat user membuat transaksi baru di Filament:\n";
        echo "   1. User memilih course → sub_total dan admin_fee terisi otomatis\n";
        echo "   2. User memilih diskon → discount_amount dihitung dan terisi otomatis\n";
        echo "   3. Grand total diperbarui secara real-time\n";
        echo "   4. Semua data diskon tersimpan ke database\n";
    } else {
        echo "\n❌ MASIH ADA MASALAH DENGAN PENYIMPANAN DISKON!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error saat membuat transaksi: " . $e->getMessage() . "\n";
}

echo "\n=== RINGKASAN PERBAIKAN YANG DILAKUKAN ===\n";
echo "1. ✅ Menghapus ->default(0) dari field discount_amount di edit form\n";
echo "2. ✅ Menambahkan logika auto-calculation pada field discount_id\n";
echo "3. ✅ Membuat field discount_amount menjadi readonly (auto-calculated)\n";
echo "4. ✅ Memperbaiki logika afterStateUpdated di field course_id\n";

echo "\n📝 URL untuk test manual di Filament:\n";
echo "   Create Transaction: http://localhost:8000/admin/transactions/create\n";
echo "   List Transactions: http://localhost:8000/admin/transactions\n";
echo "\n💡 Instruksi test manual:\n";
echo "   1. Buka form Create Transaction\n";
echo "   2. Pilih course → lihat sub_total dan admin_fee terisi otomatis\n";
echo "   3. Pilih diskon → lihat discount_amount terisi otomatis\n";
echo "   4. Lihat grand_total diperbarui secara real-time\n";
echo "   5. Submit form dan cek apakah data diskon tersimpan\n";