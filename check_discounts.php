<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Discount;

echo "=== Available Discounts ===\n\n";

$discounts = Discount::where('is_active', true)->get(['id', 'name', 'code', 'type', 'value']);

if ($discounts->count() > 0) {
    foreach ($discounts as $discount) {
        echo "- {$discount->name} ({$discount->code}): {$discount->value}";
        echo ($discount->type === 'percentage' ? '%' : ' Rupiah');
        echo "\n";
    }
} else {
    echo "No active discounts found.\n";
}

echo "\n";