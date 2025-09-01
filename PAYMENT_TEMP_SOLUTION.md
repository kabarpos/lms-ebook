# Payment Temp Solution - Discount Data Preservation

## Problem
Midtrans webhook notifications sometimes receive `custom_expiry` as `null`, causing discount data to be lost during transaction creation. This resulted in transactions being created without proper discount amounts and discount IDs.

## Root Cause
The issue occurs when:
1. Midtrans doesn't send `custom_expiry` in webhook notifications
2. Middleware or network issues modify webhook data
3. Midtrans configuration doesn't properly support `custom_expiry` field

## Solution: Payment Temp Table

### Implementation
1. **Created `payment_temp` table** to store payment data temporarily
2. **Updated PaymentService** to save payment data after successful snap token creation
3. **Modified webhook handling** to use payment_temp as fallback when custom_expiry is null
4. **Added automatic cleanup** for expired records

### Database Schema
```sql
CREATE TABLE payment_temp (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(255) UNIQUE NOT NULL,
    user_id BIGINT NOT NULL,
    course_id BIGINT NOT NULL,
    sub_total_amount DECIMAL(15,2) NOT NULL,
    admin_fee_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    discount_id BIGINT NULL,
    grand_total_amount DECIMAL(15,2) NOT NULL,
    snap_token TEXT NOT NULL,
    discount_data JSON NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Key Features

#### 1. Automatic Data Preservation
- Payment data is saved to `payment_temp` immediately after snap token creation
- Includes all discount information (amount, ID, and full discount data)
- Records expire automatically after 2 hours

#### 2. Fallback Mechanism
```php
// In PaymentService::createCourseTransaction()
if (empty($notification['custom_expiry']) || ($discountAmount == 0 && $discountId === null)) {
    $paymentTemp = PaymentTemp::findByOrderId($notification['order_id']);
    if ($paymentTemp) {
        $discountAmount = $paymentTemp->discount_amount ?? 0;
        $discountId = $paymentTemp->discount_id;
        // Use payment_temp data
    }
}
```

#### 3. Automatic Cleanup
- Records are deleted after successful transaction creation
- Expired records are cleaned up via artisan command
- Command: `php artisan payment:cleanup-expired`

### Benefits

1. **Reliability**: Discount data is preserved even when Midtrans fails to send custom_expiry
2. **Transparency**: Full logging of data source (custom_expiry vs payment_temp)
3. **Performance**: Minimal overhead, records are cleaned up automatically
4. **Backward Compatibility**: Still uses custom_expiry when available
5. **Data Integrity**: Complete discount information is preserved

### Testing Results

✅ **Payment Creation**: Successfully creates payment_temp records with discount data  
✅ **Webhook Fallback**: Correctly uses payment_temp when custom_expiry is null  
✅ **Transaction Creation**: Discount amounts and IDs are properly applied  
✅ **Cleanup**: Automatic cleanup of completed and expired records  
✅ **Command**: Manual cleanup command works correctly  

### Usage

#### Monitor Payment Temp Records
```php
// Check current payment_temp records
$records = PaymentTemp::with(['user', 'course', 'discount'])->get();

// Find by order ID
$payment = PaymentTemp::findByOrderId('DC1234');

// Get discount info
$discountData = $payment->getDiscountInfo();
```

#### Manual Cleanup
```bash
# Clean up expired records
php artisan payment:cleanup-expired

# Can be added to cron job for automatic cleanup
0 */6 * * * cd /path/to/project && php artisan payment:cleanup-expired
```

### Files Modified

1. **Migration**: `database/migrations/2025_09_01_064418_create_payment_temp_table.php`
2. **Model**: `app/Models/PaymentTemp.php`
3. **Service**: `app/Services/PaymentService.php`
4. **Command**: `app/Console/Commands/CleanupExpiredPaymentTemp.php`
5. **Test**: `test_payment_temp_solution.php`

### Monitoring

Check logs for these entries:
- `Payment temp record created successfully`
- `USING PAYMENT_TEMP DATA (CUSTOM_EXPIRY FALLBACK)`
- `Payment temp record cleaned up`
- `Payment temp cleanup completed`

This solution ensures that discount data is never lost, providing a robust fallback mechanism for the Midtrans integration.