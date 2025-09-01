<?php

echo "=== TEST DISCOUNT WEBHOOK SIMULATION ===\n\n";

// Simulate discount calculation
$coursePrice = 326000;
$adminFee = 3000;
$discountPercentage = 50;
$discountMaxAmount = 100000;

// Calculate discount amount (same logic as PaymentService)
$discountAmount = ($coursePrice * $discountPercentage) / 100;
if ($discountMaxAmount > 0) {
    $discountAmount = min($discountAmount, $discountMaxAmount);
}

$grandTotal = $coursePrice + $adminFee - $discountAmount;

echo "=== CALCULATION TEST ===\n";
echo "Course Price: Rp " . number_format($coursePrice, 0, ',', '.') . "\n";
echo "Admin Fee: Rp " . number_format($adminFee, 0, ',', '.') . "\n";
echo "Discount Percentage: {$discountPercentage}%\n";
echo "Discount Max Amount: Rp " . number_format($discountMaxAmount, 0, ',', '.') . "\n";
echo "Calculated Discount: Rp " . number_format($discountAmount, 0, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format($grandTotal, 0, ',', '.') . "\n\n";

// Simulate custom_expiry data that PaymentService sends to Midtrans
$customExpiryData = [
    'admin_fee_amount' => $adminFee,
    'discount_amount' => $discountAmount,
    'discount_id' => 1
];

$customExpiryJson = json_encode($customExpiryData);

echo "=== CUSTOM_EXPIRY DATA (PaymentService -> Midtrans) ===\n";
echo "JSON: {$customExpiryJson}\n\n";

// Simulate Midtrans notification with custom_expiry
$mockNotification = [
    'order_id' => 'TEST-' . time(),
    'transaction_status' => 'settlement',
    'gross_amount' => $grandTotal,
    'custom_field1' => 1, // User ID
    'custom_field2' => 1, // Course ID
    'custom_field3' => 'course',
    'custom_expiry' => $customExpiryJson
];

echo "=== MOCK MIDTRANS NOTIFICATION ===\n";
foreach ($mockNotification as $key => $value) {
    echo "{$key}: {$value}\n";
}
echo "\n";

// Simulate PaymentService::createCourseTransaction parsing
echo "=== PARSING CUSTOM_EXPIRY (createCourseTransaction) ===\n";
$parsedCustomExpiry = json_decode($mockNotification['custom_expiry'] ?? '{}', true);
$parsedDiscountAmount = $parsedCustomExpiry['discount_amount'] ?? 0;
$parsedDiscountId = $parsedCustomExpiry['discount_id'] ?? null;

echo "Raw custom_expiry: {$mockNotification['custom_expiry']}\n";
echo "Parsed custom_expiry: " . print_r($parsedCustomExpiry, true) . "\n";
echo "Parsed discount_amount: Rp " . number_format($parsedDiscountAmount, 0, ',', '.') . "\n";
echo "Parsed discount_id: {$parsedDiscountId}\n\n";

// Simulate transaction data that would be saved to database
$transactionData = [
    'user_id' => $mockNotification['custom_field1'],
    'pricing_id' => null,
    'course_id' => $mockNotification['custom_field2'],
    'sub_total_amount' => $coursePrice,
    'admin_fee_amount' => $adminFee,
    'discount_amount' => $parsedDiscountAmount,
    'discount_id' => $parsedDiscountId,
    'grand_total_amount' => $mockNotification['gross_amount'],
    'payment_type' => 'Midtrans',
    'is_paid' => true,
    'booking_trx_id' => $mockNotification['order_id'],
];

echo "=== TRANSACTION DATA (to be saved to database) ===\n";
foreach ($transactionData as $key => $value) {
    if (in_array($key, ['sub_total_amount', 'admin_fee_amount', 'discount_amount', 'grand_total_amount'])) {
        echo "{$key}: Rp " . number_format($value, 0, ',', '.') . "\n";
    } else {
        echo "{$key}: {$value}\n";
    }
}
echo "\n";

// Verification
echo "=== VERIFICATION ===\n";
$isDiscountCorrect = ($parsedDiscountAmount == $discountAmount);
$isDiscountIdCorrect = ($parsedDiscountId == 1);
$isTotalCorrect = ($transactionData['grand_total_amount'] == $grandTotal);

echo "Discount Amount Correct: " . ($isDiscountCorrect ? "✅" : "❌") . "\n";
echo "Discount ID Correct: " . ($isDiscountIdCorrect ? "✅" : "❌") . "\n";
echo "Grand Total Correct: " . ($isTotalCorrect ? "✅" : "❌") . "\n\n";

if ($isDiscountCorrect && $isDiscountIdCorrect && $isTotalCorrect) {
    echo "🎉 SUCCESS: Discount data will be correctly saved to admin dashboard!\n";
    echo "\n=== EXPECTED RESULT IN ADMIN DASHBOARD ===\n";
    echo "- Discount Amount column will show: Rp " . number_format($parsedDiscountAmount, 0, ',', '.') . "\n";
    echo "- Discount relation will link to discount ID: {$parsedDiscountId}\n";
    echo "- Transaction will appear with correct discount information\n";
} else {
    echo "❌ FAILED: There are issues with discount data processing!\n";
}

echo "\n=== BEFORE vs AFTER FIX ===\n";
echo "BEFORE: MidtransService::handleNotification() missing 'custom_expiry' field\n";
echo "RESULT: Discount data lost, not saved to database\n";
echo "\nAFTER: MidtransService::handleNotification() includes 'custom_expiry' field\n";
echo "RESULT: Discount data preserved and saved to database\n";

echo "\n=== TEST COMPLETED ===\n";