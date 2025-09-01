<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;
use Carbon\Carbon;

echo "=== CHECKING PROBLEMATIC DISCOUNTS ===\n";
echo "Current time: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

// Check FLASH50
$flash50 = Discount::where('code', 'FLASH50')->first();
if ($flash50) {
    echo "=== FLASH50 ===\n";
    echo "Start Date: " . ($flash50->start_date ? $flash50->start_date->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "End Date: " . ($flash50->end_date ? $flash50->end_date->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Is Active: " . ($flash50->is_active ? 'Yes' : 'No') . "\n";
    echo "Current time vs start: " . (Carbon::now()->gte($flash50->start_date) ? 'After start' : 'Before start') . "\n";
    echo "Current time vs end: " . (Carbon::now()->lte($flash50->end_date) ? 'Before end' : 'After end') . "\n";
    
    // Fix FLASH50 - extend end date
    echo "\nFixing FLASH50...\n";
    $flash50->update([
        'end_date' => Carbon::now()->addMonths(3)
    ]);
    echo "FLASH50 end date updated to: " . $flash50->fresh()->end_date->format('Y-m-d H:i:s') . "\n";
} else {
    echo "FLASH50 not found\n";
}

echo "\n" . str_repeat('-', 50) . "\n\n";

// Check SAPI50
$sapi50 = Discount::where('code', 'SAPI50')->first();
if ($sapi50) {
    echo "=== SAPI50 ===\n";
    echo "Start Date: " . ($sapi50->start_date ? $sapi50->start_date->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "End Date: " . ($sapi50->end_date ? $sapi50->end_date->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "Is Active: " . ($sapi50->is_active ? 'Yes' : 'No') . "\n";
    echo "Minimum Amount: " . $sapi50->minimum_amount . "\n";
    echo "Current time vs start: " . (Carbon::now()->gte($sapi50->start_date) ? 'After start' : 'Before start') . "\n";
    echo "Current time vs end: " . (Carbon::now()->lte($sapi50->end_date) ? 'Before end' : 'After end') . "\n";
    
    // Fix SAPI50 - set start date to now and reduce minimum amount
    echo "\nFixing SAPI50...\n";
    $sapi50->update([
        'start_date' => Carbon::now(),
        'minimum_amount' => 100000 // Reduce minimum amount
    ]);
    $updated = $sapi50->fresh();
    echo "SAPI50 start date updated to: " . $updated->start_date->format('Y-m-d H:i:s') . "\n";
    echo "SAPI50 minimum amount updated to: " . $updated->minimum_amount . "\n";
} else {
    echo "SAPI50 not found\n";
}

echo "\n" . str_repeat('-', 50) . "\n\n";

// Check all inactive or problematic discounts
echo "=== ALL DISCOUNTS STATUS ===\n";
$allDiscounts = Discount::all();
foreach ($allDiscounts as $discount) {
    $isActiveScope = Discount::active()->where('code', $discount->code)->exists();
    $isAvailableScope = Discount::available()->where('code', $discount->code)->exists();
    $isBothScope = Discount::active()->available()->where('code', $discount->code)->exists();
    
    echo "Code: {$discount->code}\n";
    echo "  Active Scope: " . ($isActiveScope ? 'Yes' : 'No') . "\n";
    echo "  Available Scope: " . ($isAvailableScope ? 'Yes' : 'No') . "\n";
    echo "  Both Scopes: " . ($isBothScope ? 'Yes' : 'No') . "\n";
    
    if (!$isBothScope) {
        echo "  PROBLEM: Not found in combined scope!\n";
        if (!$isActiveScope) {
            echo "    - Not active (check dates and is_active flag)\n";
        }
        if (!$isAvailableScope) {
            echo "    - Not available (check usage limit)\n";
        }
    }
    echo "\n";
}

echo "=== DONE ===\n";