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

echo "=== TEST PEMBUATAN TRANSAKSI BARU DENGAN DISKON ===\n\n";

// Buat atau ambil data yang diperlukan
$category = Category::first() ?? Category::create([
    'name' => 'Test Category',
    'slug' => 'test-category',
    'icon' => 'test-icon'
]);

$course = Course::first() ?? Course::create([
    'name' => 'Test Course for Transaction',
    'slug' => 'test-course-transaction',
    'thumbnail' => 'test.jpg',
    'about' => 'Test course description',
    'price' => 299000,
    'admin_fee_amount' => 7500,
    'category_id' => $category->id,
]);

$user = User::where('email', 'test@example.com')->first() ?? User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
    'whatsapp_number' => '081234567890',
]);

$discount = Discount::where('code', 'TEST30')->first() ?? Discount::create([
    'name' => 'Test Discount 30%',
    'code' => 'TEST30',
    'type' => 'percentage',
    'value' => 30,
    'is_active' => true,
    'start_date' => now()->subDay(),
    'end_date' => now()->addMonth(),
]);

echo "Data yang digunakan:\n";
echo "- Course: {$course->name} (Rp " . number_format($course->price) . ")\n";
echo "- Admin Fee: Rp " . number_format($course->admin_fee_amount) . "\n";
echo "- User: {$user->name} ({$user->email})\n";
echo "- Discount: {$discount->name} ({$discount->code}) - {$discount->value}%\n\n";

// Hitung nilai yang diharapkan
$subTotal = $course->price;
$adminFee = $course->admin_fee_amount;
$discountAmount = ($subTotal * $discount->value) / 100;
$grandTotal = $subTotal + $adminFee - $discountAmount;

echo "Kalkulasi yang diharapkan:\n";
echo "- Sub Total: Rp " . number_format($subTotal) . "\n";
echo "- Admin Fee: Rp " . number_format($adminFee) . "\n";
echo "- Discount Amount: Rp " . number_format($discountAmount) . "\n";
echo "- Grand Total: Rp " . number_format($grandTotal) . "\n\n";

// Simulasi data yang akan dikirim dari form Filament
$formData = [
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

echo "Data form yang akan disimpan:\n";
foreach ($formData as $key => $value) {
    if (in_array($key, ['sub_total_amount', 'admin_fee_amount', 'discount_amount', 'grand_total_amount'])) {
        echo "- {$key}: Rp " . number_format($value) . "\n";
    } else {
        echo "- {$key}: {$value}\n";
    }
}
echo "\n";

// Test 1: Buat transaksi langsung menggunakan model
echo "=== TEST 1: PEMBUATAN TRANSAKSI LANGSUNG ===\n";
try {
    $transaction = Transaction::create($formData);
    echo "✓ Transaksi berhasil dibuat dengan ID: {$transaction->id}\n";
    
    // Verifikasi data yang tersimpan
    $savedTransaction = Transaction::with(['discount', 'course', 'student'])->find($transaction->id);
    
    echo "\nData yang tersimpan di database:\n";
    echo "- Booking ID: {$savedTransaction->booking_trx_id}\n";
    echo "- Course: {$savedTransaction->course->name}\n";
    echo "- Student: {$savedTransaction->student->name}\n";
    echo "- Sub Total: Rp " . number_format($savedTransaction->sub_total_amount) . "\n";
    echo "- Admin Fee: Rp " . number_format($savedTransaction->admin_fee_amount) . "\n";
    echo "- Discount Amount: Rp " . number_format($savedTransaction->discount_amount) . "\n";
    echo "- Discount ID: {$savedTransaction->discount_id}\n";
    echo "- Discount Name: " . ($savedTransaction->discount ? $savedTransaction->discount->name : 'NULL') . "\n";
    echo "- Grand Total: Rp " . number_format($savedTransaction->grand_total_amount) . "\n";
    
    // Verifikasi apakah data diskon tersimpan dengan benar
    if ($savedTransaction->discount_amount == $discountAmount && $savedTransaction->discount_id == $discount->id) {
        echo "\n✓ DATA DISKON TERSIMPAN DENGAN BENAR!\n";
    } else {
        echo "\n❌ DATA DISKON TIDAK TERSIMPAN DENGAN BENAR!\n";
        echo "Expected discount_amount: " . $discountAmount . ", Got: " . $savedTransaction->discount_amount . "\n";
        echo "Expected discount_id: " . $discount->id . ", Got: " . $savedTransaction->discount_id . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error saat membuat transaksi: " . $e->getMessage() . "\n";
}

echo "\n=== TEST 2: VERIFIKASI FILLABLE FIELDS ===\n";
$transactionModel = new Transaction();
$fillableFields = $transactionModel->getFillable();

echo "Fillable fields di model Transaction:\n";
foreach ($fillableFields as $field) {
    echo "- {$field}\n";
}

$requiredDiscountFields = ['discount_amount', 'discount_id'];
echo "\nVerifikasi field diskon:\n";
foreach ($requiredDiscountFields as $field) {
    if (in_array($field, $fillableFields)) {
        echo "✓ {$field} ada dalam fillable\n";
    } else {
        echo "❌ {$field} TIDAK ada dalam fillable\n";
    }
}

echo "\n=== KESIMPULAN ===\n";
echo "Jika test di atas menunjukkan data diskon tersimpan dengan benar,\n";
echo "maka masalah kemungkinan ada di:\n";
echo "1. Form validation di Filament\n";
echo "2. JavaScript yang mengubah nilai sebelum submit\n";
echo "3. Method custom di Filament yang belum ditemukan\n";
echo "\nSilakan cek form Filament untuk membuat transaksi baru dan\n";
echo "pastikan field discount_amount dan discount_id terisi dengan benar.\n";

echo "\n📝 URL untuk test manual:\n";
echo "   Create Transaction: http://localhost:8000/admin/transactions/create\n";
echo "   List Transactions: http://localhost:8000/admin/transactions\n";