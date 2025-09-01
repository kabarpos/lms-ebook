<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use App\Models\Discount;

echo "\n=== MONITOR REAL CHECKOUT LOGS ===\n";
echo "Monitoring log file untuk checkout real-time...\n";
echo "Silakan lakukan checkout di browser, log akan muncul di sini.\n";
echo "Tekan Ctrl+C untuk berhenti.\n\n";

// Path ke log file Laravel
$logPath = storage_path('logs/laravel.log');

if (!file_exists($logPath)) {
    echo "❌ Log file tidak ditemukan: $logPath\n";
    exit(1);
}

// Get current file size to start monitoring from end
$lastSize = filesize($logPath);
echo "📊 Memulai monitoring dari posisi: $lastSize bytes\n\n";

// Keywords yang akan dimonitor
$keywords = [
    '=== PAYMENT REQUEST RECEIVED ===',
    '=== PAYMENT SERVICE START ===',
    'Checking session for discount',
    'Discount found in session',
    'No discount found in session',
    'Final discount calculation',
    'Preparing Midtrans parameters',
    '=== PARSING DISCOUNT FROM CUSTOM_EXPIRY ===',
    'Course transaction data to be created',
    'Course transaction successfully created'
];

while (true) {
    clearstatcache();
    $currentSize = filesize($logPath);
    
    if ($currentSize > $lastSize) {
        // Read new content
        $handle = fopen($logPath, 'r');
        fseek($handle, $lastSize);
        $newContent = fread($handle, $currentSize - $lastSize);
        fclose($handle);
        
        // Split into lines
        $lines = explode("\n", $newContent);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Check if line contains any of our keywords
            foreach ($keywords as $keyword) {
                if (strpos($line, $keyword) !== false) {
                    echo "\n" . date('H:i:s') . " | " . $line . "\n";
                    
                    // If it's a JSON log, try to parse and display nicely
                    if (strpos($line, '{') !== false) {
                        $jsonStart = strpos($line, '{');
                        $jsonPart = substr($line, $jsonStart);
                        
                        $decoded = json_decode($jsonPart, true);
                        if ($decoded && is_array($decoded)) {
                            echo "   📋 Data: " . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                        }
                    }
                    break;
                }
            }
        }
        
        $lastSize = $currentSize;
    }
    
    // Sleep for a short time
    usleep(500000); // 0.5 seconds
}

?>