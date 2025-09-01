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
use App\Models\MidtransSetting;

echo "=== DEBUG MIDTRANS WEBHOOK FIELDS ===\n";
echo "Tujuan: Menguji field apa saja yang dikirim Midtrans dalam webhook\n\n";

// 1. Check Midtrans configuration
echo "1. Checking Midtrans configuration...\n";
$config = MidtransSetting::getActiveConfig();
if ($config) {
    echo "   ✓ Active config found\n";
    echo "   - Environment: " . ($config->is_production ? 'Production' : 'Sandbox') . "\n";
    echo "   - Merchant ID: {$config->merchant_id}\n";
    echo "   - Server Key: " . substr($config->server_key, 0, 10) . "...\n";
} else {
    echo "   ✗ No active Midtrans configuration found\n";
    exit(1);
}

// 2. Check recent webhook logs
echo "\n2. Checking recent webhook logs...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -200); // Last 200 lines
    
    // Find webhook notifications
    $webhookLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'Received Midtrans notification:') !== false;
    });
    
    if (!empty($webhookLogs)) {
        echo "   📊 Recent webhook notifications found:\n";
        foreach (array_slice($webhookLogs, -3) as $log) { // Last 3 webhooks
            echo "      " . trim($log) . "\n";
            
            // Extract JSON data from log
            if (preg_match('/\{.*\}/', $log, $matches)) {
                $jsonData = $matches[0];
                $data = json_decode($jsonData, true);
                if ($data) {
                    echo "        Fields received:\n";
                    foreach ($data as $key => $value) {
                        if ($key === 'custom_expiry') {
                            echo "          - {$key}: " . ($value === null ? 'NULL' : $value) . "\n";
                            if ($value !== null) {
                                $parsed = json_decode($value, true);
                                if ($parsed) {
                                    echo "            Parsed: " . print_r($parsed, true);
                                }
                            }
                        } else {
                            echo "          - {$key}: {$value}\n";
                        }
                    }
                    echo "\n";
                }
            }
        }
    } else {
        echo "   ⚠️ No webhook notifications found in recent logs\n";
    }
} else {
    echo "   ✗ Log file not found\n";
}

// 3. Check Midtrans payment creation logs
echo "\n3. Checking payment creation logs...\n";
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -200);
    
    // Find payment creation logs with custom_expiry
    $paymentLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'custom_expiry') !== false && strpos($line, 'Midtrans parameters') !== false;
    });
    
    if (!empty($paymentLogs)) {
        echo "   📊 Payment creation with custom_expiry found:\n";
        foreach (array_slice($paymentLogs, -2) as $log) { // Last 2 payments
            echo "      " . trim($log) . "\n";
        }
    } else {
        echo "   ⚠️ No payment creation logs with custom_expiry found\n";
    }
}

// 4. Test webhook endpoint manually
echo "\n4. Testing webhook endpoint accessibility...\n";
$webhookUrl = url('/booking/payment/midtrans/notification');
echo "   Webhook URL: {$webhookUrl}\n";

// Check if route exists
try {
    $route = app('router')->getRoutes()->match(
        Illuminate\Http\Request::create($webhookUrl, 'POST')
    );
    echo "   ✓ Webhook route exists and accessible\n";
    echo "   - Controller: " . $route->getActionName() . "\n";
} catch (Exception $e) {
    echo "   ✗ Webhook route not accessible: {$e->getMessage()}\n";
}

// 5. Recommendations
echo "\n=== RECOMMENDATIONS ===\n";
echo "Berdasarkan analisis, kemungkinan masalah:\n\n";

echo "1. 🔧 KONFIGURASI MIDTRANS DASHBOARD:\n";
echo "   - Login ke Midtrans Dashboard (" . ($config->is_production ? 'Production' : 'Sandbox') . ")\n";
echo "   - Masuk ke Settings > Configuration\n";
echo "   - Pastikan Payment Notification URL: {$webhookUrl}\n";
echo "   - Pastikan 'Append notification with HTTP POST' diaktifkan\n";
echo "   - Pastikan semua custom fields diaktifkan\n\n";

echo "2. 🧪 TEST MANUAL:\n";
echo "   - Buat transaksi baru dengan diskon\n";
echo "   - Monitor log real-time: php monitor_real_checkout_logs.php\n";
echo "   - Periksa apakah custom_expiry dikirim oleh Midtrans\n\n";

echo "3. 🔍 DEBUGGING LANJUTAN:\n";
echo "   - Jika custom_expiry masih NULL, masalah di konfigurasi Midtrans\n";
echo "   - Jika custom_expiry ada tapi tidak di-parse, masalah di kode\n";
echo "   - Gunakan Midtrans simulator untuk test webhook\n\n";

echo "4. 🚨 SOLUSI ALTERNATIF:\n";
echo "   - Simpan data diskon di database saat payment creation\n";
echo "   - Gunakan order_id untuk lookup data diskon saat webhook\n";
echo "   - Tidak bergantung pada custom_expiry dari Midtrans\n\n";

echo "=== DEBUG SELESAI ===\n";