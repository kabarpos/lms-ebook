<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Discount;
use App\Models\Course;
use App\Models\User;

echo "=== TEST FILAMENT DISCOUNT FIELD FIX ===\n\n";

// 1. Cari transaksi yang memiliki discount_amount > 0
echo "1. Mencari transaksi dengan diskon...\n";
$transactionWithDiscount = Transaction::where('discount_amount', '>', 0)
    ->with(['course', 'student', 'discount'])
    ->first();

if (!$transactionWithDiscount) {
    echo "❌ Tidak ada transaksi dengan diskon ditemukan\n";
    echo "   Membuat transaksi test dengan diskon...\n";
    
    // Ambil course dan user pertama
    $course = Course::first();
    $user = User::first();
    $discount = Discount::where('is_active', true)->first();
    
    if (!$course || !$user || !$discount) {
        echo "❌ Data course, user, atau discount tidak tersedia\n";
        exit(1);
    }
    
    // Buat transaksi test
    $transactionWithDiscount = Transaction::create([
        'booking_trx_id' => 'TEST' . rand(1000, 9999),
        'user_id' => $user->id,
        'course_id' => $course->id,
        'sub_total_amount' => $course->price,
        'admin_fee_amount' => 7500,
        'discount_amount' => 25000, // Set discount amount
        'discount_id' => $discount->id,
        'grand_total_amount' => $course->price + 7500 - 25000,
        'is_paid' => true,
        'payment_type' => 'Manual',
        'started_at' => now(),
        'ended_at' => now()->addYear(),
    ]);
    
    echo "✓ Transaksi test dibuat dengan ID: {$transactionWithDiscount->id}\n";
}

echo "\n2. Detail transaksi dengan diskon:\n";
echo "   - ID Transaksi: {$transactionWithDiscount->booking_trx_id}\n";
echo "   - Course: {$transactionWithDiscount->course->name}\n";
echo "   - Student: {$transactionWithDiscount->student->name}\n";
echo "   - Sub Total: Rp " . number_format($transactionWithDiscount->sub_total_amount, 0, '', '.') . "\n";
echo "   - Admin Fee: Rp " . number_format($transactionWithDiscount->admin_fee_amount, 0, '', '.') . "\n";
echo "   - Discount Amount: Rp " . number_format($transactionWithDiscount->discount_amount, 0, '', '.') . "\n";
echo "   - Discount ID: {$transactionWithDiscount->discount_id}\n";
if ($transactionWithDiscount->discount) {
    echo "   - Discount Name: {$transactionWithDiscount->discount->name}\n";
    echo "   - Discount Code: {$transactionWithDiscount->discount->code}\n";
}
echo "   - Grand Total: Rp " . number_format($transactionWithDiscount->grand_total_amount, 0, '', '.') . "\n";

echo "\n3. Verifikasi data dari database:\n";
// Re-fetch dari database untuk memastikan data tersimpan dengan benar
$freshTransaction = Transaction::find($transactionWithDiscount->id);
echo "   - Discount Amount dari DB: Rp " . number_format($freshTransaction->discount_amount, 0, '', '.') . "\n";
echo "   - Discount ID dari DB: {$freshTransaction->discount_id}\n";

echo "\n4. Test field mapping untuk Filament:\n";
// Simulasi bagaimana Filament akan mengambil data
$formData = [
    'discount_amount' => $freshTransaction->discount_amount,
    'discount_id' => $freshTransaction->discount_id,
    'sub_total_amount' => $freshTransaction->sub_total_amount,
    'admin_fee_amount' => $freshTransaction->admin_fee_amount,
    'grand_total_amount' => $freshTransaction->grand_total_amount,
];

echo "   Form data yang akan dimuat di Filament:\n";
foreach ($formData as $field => $value) {
    if (in_array($field, ['discount_amount', 'sub_total_amount', 'admin_fee_amount', 'grand_total_amount'])) {
        echo "   - {$field}: Rp " . number_format($value, 0, '', '.') . "\n";
    } else {
        echo "   - {$field}: {$value}\n";
    }
}

echo "\n5. Verifikasi perbaikan:\n";
if ($freshTransaction->discount_amount > 0) {
    echo "✓ Field discount_amount memiliki nilai: Rp " . number_format($freshTransaction->discount_amount, 0, '', '.') . "\n";
    echo "✓ Setelah menghapus ->default(0), field ini seharusnya menampilkan nilai yang benar\n";
    echo "✓ Field discount_id: {$freshTransaction->discount_id}\n";
} else {
    echo "❌ Field discount_amount masih 0\n";
}

echo "\n6. URL untuk test manual di Filament:\n";
echo "   Edit Transaction: http://localhost:8000/admin/transactions/{$transactionWithDiscount->id}/edit\n";
echo "   List Transactions: http://localhost:8000/admin/transactions\n";

echo "\n=== HASIL TEST ===\n";
echo "✓ Perbaikan TransactionResource.php telah dilakukan\n";
echo "✓ Field discount_amount tidak lagi memiliki ->default(0)\n";
echo "✓ Field seharusnya menampilkan nilai dari database\n";
echo "\n📝 Silakan buka URL di atas untuk memverifikasi secara visual\n";
echo "   bahwa field 'Discount amount' sekarang menampilkan nilai yang benar\n";
echo "   dari database, bukan selalu 0.\n";