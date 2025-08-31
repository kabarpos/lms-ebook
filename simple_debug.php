<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application properly
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->boot();

echo "=== SIMPLE DEBUG DISCOUNT SYSTEM ===\n\n";

try {
    // Test 1: Check database connection
    echo "1. TESTING DATABASE CONNECTION:\n";
    $pdo = DB::connection()->getPdo();
    echo "✓ Database connected successfully\n";
    echo "  - Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "  - Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
    // Test 2: Check if course exists
    echo "2. TESTING COURSE EXISTENCE:\n";
    $courseCount = DB::table('courses')->count();
    echo "Total courses in database: {$courseCount}\n";
    
    $course = DB::table('courses')->where('slug', 'laravel-untuk-pemula')->first();
    if ($course) {
        echo "✓ Course found: {$course->name} (ID: {$course->id})\n";
        echo "  - Price: Rp " . number_format($course->price, 0, ',', '.') . "\n";
    } else {
        echo "✗ Course not found with slug 'laravel-untuk-pemula'\n";
        $courses = DB::table('courses')->limit(5)->get(['id', 'name', 'slug']);
        echo "Available courses:\n";
        foreach ($courses as $c) {
            echo "  - {$c->slug} ({$c->name})\n";
        }
    }
    echo "\n";
    
    // Test 3: Check discount table
    echo "3. TESTING DISCOUNT TABLE:\n";
    $discountCount = DB::table('discounts')->count();
    echo "Total discounts in database: {$discountCount}\n";
    
    $discount = DB::table('discounts')->where('code', 'FLASH50')->first();
    if ($discount) {
        echo "✓ Discount FLASH50 found:\n";
        echo "  - Name: {$discount->name}\n";
        echo "  - Code: {$discount->code}\n";
        echo "  - Type: {$discount->type}\n";
        echo "  - Value: {$discount->value}\n";
        echo "  - Active: " . ($discount->is_active ? 'Yes' : 'No') . "\n";
        echo "  - Valid from: {$discount->valid_from}\n";
        echo "  - Valid until: {$discount->valid_until}\n";
        echo "  - Created: {$discount->created_at}\n";
    } else {
        echo "✗ Discount 'FLASH50' not found\n";
        $discounts = DB::table('discounts')->limit(5)->get(['code', 'name', 'is_active']);
        echo "Available discounts:\n";
        foreach ($discounts as $d) {
            $status = $d->is_active ? 'Active' : 'Inactive';
            echo "  - {$d->code} ({$d->name}) - {$status}\n";
        }
    }
    echo "\n";
    
    // Test 4: Check tables structure
    echo "4. TESTING TABLE STRUCTURE:\n";
    $tables = DB::select('SHOW TABLES');
    $tableNames = [];
    foreach ($tables as $table) {
        $tableArray = (array) $table;
        $tableNames[] = array_values($tableArray)[0];
    }
    echo "Available tables: " . implode(', ', $tableNames) . "\n";
    
    // Check if discount-related tables exist
    $requiredTables = ['courses', 'discounts', 'transactions', 'transaction_details'];
    foreach ($requiredTables as $tableName) {
        if (in_array($tableName, $tableNames)) {
            echo "✓ Table '{$tableName}' exists\n";
        } else {
            echo "✗ Table '{$tableName}' missing\n";
        }
    }
    echo "\n";
    
    // Test 5: Check if we can create a simple discount validation
    if ($discount && $course) {
        echo "5. TESTING SIMPLE DISCOUNT CALCULATION:\n";
        $originalPrice = $course->price;
        $discountValue = $discount->value;
        $discountType = $discount->type;
        
        if ($discountType === 'percentage') {
            $discountAmount = ($originalPrice * $discountValue) / 100;
            $finalPrice = $originalPrice - $discountAmount;
        } else {
            $discountAmount = $discountValue;
            $finalPrice = $originalPrice - $discountAmount;
        }
        
        echo "✓ Manual calculation successful:\n";
        echo "  - Original price: Rp " . number_format($originalPrice, 0, ',', '.') . "\n";
        echo "  - Discount type: {$discountType}\n";
        echo "  - Discount value: {$discountValue}\n";
        echo "  - Discount amount: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
        echo "  - Final price: Rp " . number_format($finalPrice, 0, ',', '.') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";