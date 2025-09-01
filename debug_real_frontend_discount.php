<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a proper HTTP request context
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\User;
use App\Models\Discount;
use App\Models\Transaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUG REAL FRONTEND DISCOUNT ISSUE ===\n";
echo "Tujuan: Menginvestigasi mengapa diskon tidak dikirim dari frontend real\n\n";

// 1. Analisis transaksi real terbaru
echo "1. Analisis transaksi real terbaru...\n";
$latestTransaction = Transaction::latest()->first();

if ($latestTransaction) {
    echo "   📊 Transaksi terbaru:\n";
    echo "      - ID: {$latestTransaction->id}\n";
    echo "      - Order ID: {$latestTransaction->booking_trx_id}\n";
    echo "      - User ID: {$latestTransaction->user_id}\n";
    echo "      - Course ID: {$latestTransaction->course_id}\n";
    echo "      - Sub Total: Rp " . number_format($latestTransaction->sub_total_amount, 0, ',', '.') . "\n";
    echo "      - Admin Fee: Rp " . number_format($latestTransaction->admin_fee_amount, 0, ',', '.') . "\n";
    echo "      - Discount Amount: Rp " . number_format($latestTransaction->discount_amount, 0, ',', '.') . "\n";
    echo "      - Discount ID: {$latestTransaction->discount_id}\n";
    echo "      - Grand Total: Rp " . number_format($latestTransaction->grand_total_amount, 0, ',', '.') . "\n";
    
    if ($latestTransaction->discount_amount == 0 && $latestTransaction->discount_id == null) {
        echo "   ❌ KONFIRMASI: Diskon tidak tersimpan di transaksi real!\n";
    } else {
        echo "   ✅ Diskon tersimpan dengan benar\n";
    }
} else {
    echo "   ⚠️ Tidak ada transaksi ditemukan\n";
}

// 2. Cek user dan course dari transaksi real
echo "\n2. Analisis data user dan course dari transaksi real...\n";
if ($latestTransaction) {
    $user = User::find($latestTransaction->user_id);
    $course = Course::find($latestTransaction->course_id);
    
    if ($user && $course) {
        echo "   ✓ User: {$user->name} ({$user->email})\n";
        echo "   ✓ Course: {$course->name}\n";
        echo "   ✓ Course Price: Rp " . number_format($course->price, 0, ',', '.') . "\n";
        echo "   ✓ Admin Fee: Rp " . number_format($course->admin_fee_amount ?? 0, 0, ',', '.') . "\n";
        
        // Hitung expected total tanpa diskon
        $expectedTotal = $course->price + ($course->admin_fee_amount ?? 0);
        echo "   ✓ Expected Total (tanpa diskon): Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
        echo "   ✓ Actual Grand Total: Rp " . number_format($latestTransaction->grand_total_amount, 0, ',', '.') . "\n";
        
        $difference = $expectedTotal - $latestTransaction->grand_total_amount;
        if ($difference > 0) {
            echo "   🔍 PERBEDAAN: Rp " . number_format($difference, 0, ',', '.') . " (kemungkinan diskon yang tidak tercatat)\n";
        } else {
            echo "   ✓ Total sesuai (tidak ada diskon diterapkan)\n";
        }
    }
}

// 3. Cek diskon yang tersedia dan aktif
echo "\n3. Cek diskon yang tersedia dan aktif...\n";
$activeDiscounts = Discount::where('is_active', true)
    ->where('start_date', '<=', now())
    ->where('end_date', '>=', now())
    ->get();

if ($activeDiscounts->count() > 0) {
    echo "   📊 Diskon aktif yang tersedia:\n";
    foreach ($activeDiscounts as $discount) {
        echo "      - {$discount->name} ({$discount->code})\n";
        echo "        * Type: {$discount->type}\n";
        echo "        * Value: {$discount->value}\n";
        echo "        * Max Uses: {$discount->max_uses}\n";
        echo "        * Used: {$discount->used_count}\n";
        
        if ($course) {
            $discountAmount = $discount->type === 'percentage' 
                ? ($course->price * $discount->value / 100)
                : $discount->value;
            echo "        * Discount Amount untuk course ini: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
        }
        echo "\n";
    }
} else {
    echo "   ⚠️ Tidak ada diskon aktif\n";
}

// 4. Simulasi frontend request dengan diskon
echo "\n4. Simulasi frontend request dengan diskon...\n";
if ($activeDiscounts->count() > 0 && $user && $course) {
    $testDiscount = $activeDiscounts->first();
    
    echo "   🧪 Simulasi request dengan diskon: {$testDiscount->code}\n";
    
    // Login user
    Auth::login($user);
    
    // Set session seperti yang dilakukan frontend
    Session::put('course_id', $course->id);
    Session::put('applied_discount', [
        'id' => $testDiscount->id,
        'code' => $testDiscount->code,
        'name' => $testDiscount->name,
        'type' => $testDiscount->type,
        'value' => $testDiscount->value
    ]);
    
    echo "   ✓ Session setup complete\n";
    echo "   📊 Session data:\n";
    echo "      - course_id: " . Session::get('course_id') . "\n";
    echo "      - applied_discount: " . json_encode(Session::get('applied_discount'), JSON_PRETTY_PRINT) . "\n";
    
    // Simulasi request dari frontend
    $frontendPayload = [
        'course_id' => $course->id,
        'applied_discount' => [
            'id' => $testDiscount->id,
            'code' => $testDiscount->code,
            'name' => $testDiscount->name,
            'type' => $testDiscount->type,
            'value' => $testDiscount->value
        ]
    ];
    
    echo "   📊 Frontend payload yang seharusnya dikirim:\n";
    echo json_encode($frontendPayload, JSON_PRETTY_PRINT) . "\n";
    
    // Test PaymentService
    $paymentService = app(\App\Services\PaymentService::class);
    try {
        echo "   🔄 Testing PaymentService::createCoursePayment...\n";
        $snapToken = $paymentService->createCoursePayment($course->id);
        
        if ($snapToken) {
            echo "   ✅ Snap token berhasil dibuat dengan diskon\n";
            echo "   📊 Token length: " . strlen($snapToken) . "\n";
        } else {
            echo "   ❌ Gagal membuat snap token\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
    }
}

// 5. Analisis log untuk melihat perbedaan
echo "\n5. Analisis log untuk melihat perbedaan...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -100); // Last 100 lines
    
    // Cari log payment request
    $paymentLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'PAYMENT REQUEST RECEIVED') !== false;
    });
    
    if (!empty($paymentLogs)) {
        echo "   📊 Log payment request terbaru:\n";
        foreach (array_slice($paymentLogs, -3) as $log) {
            echo "      " . trim($log) . "\n";
        }
    } else {
        echo "   ⚠️ Tidak ada log payment request ditemukan\n";
    }
    
    // Cari log custom_expiry
    $customExpiryLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'custom_expiry') !== false;
    });
    
    if (!empty($customExpiryLogs)) {
        echo "   📊 Log custom_expiry terbaru:\n";
        foreach (array_slice($customExpiryLogs, -5) as $log) {
            echo "      " . trim($log) . "\n";
        }
    }
}

echo "\n=== KESIMPULAN INVESTIGASI ===\n";
echo "🔍 Berdasarkan analisis:\n";
echo "   1. Transaksi real menunjukkan custom_expiry: null\n";
echo "   2. Discount amount: 0 dan discount_id: null\n";
echo "   3. Namun total pembayaran mungkin sudah dikurangi diskon\n";
echo "\n💡 Kemungkinan masalah:\n";
echo "   1. Frontend tidak mengirim applied_discount dalam request\n";
echo "   2. Session diskon hilang sebelum payment request\n";
echo "   3. JavaScript appliedDiscount tidak ter-inisialisasi dengan benar\n";
echo "   4. Ada middleware yang menghapus session data\n";
echo "\n🔧 Langkah selanjutnya:\n";
echo "   1. Debug JavaScript di browser untuk melihat appliedDiscount\n";
echo "   2. Tambahkan logging di FrontController untuk melihat request data\n";
echo "   3. Verifikasi session persistence di frontend\n";
echo "\n=== INVESTIGASI SELESAI ===\n";