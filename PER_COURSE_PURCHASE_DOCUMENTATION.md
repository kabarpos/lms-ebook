# Per-Course Purchase System Documentation

## Overview

This documentation covers the LMS system which operates exclusively on a per-course purchase model implemented in August 2025. The subscription-based model has been completely removed from the system.

## Business Model

### Per-Course Purchase Model
- Users purchase individual courses
- Lifetime access to purchased courses
- Each course has its own price
- No recurring fees

## Database Schema Changes

### New Tables Structure

#### Courses Table
```sql
-- Added new column
ALTER TABLE courses ADD COLUMN price UNSIGNED INTEGER DEFAULT 0 AFTER is_popular;
```

#### Transactions Table
```sql
-- Added new column for course purchases
ALTER TABLE transactions ADD COLUMN course_id FOREIGN KEY NULLABLE AFTER pricing_id;
```

### Key Relationships

```php
// Course Model
public function transactions() {
    return $this->hasMany(Transaction::class);
}

public function purchasedBy() {
    return $this->belongsToMany(User::class, 'transactions')
                ->where('is_paid', true);
}

// Transaction Model
public function course() {
    return $this->belongsTo(Course::class);
}

// User Model
public function purchasedCourses() {
    return $this->belongsToMany(Course::class, 'transactions')
                ->where('is_paid', true);
}
```

## API Endpoints

### Course Purchase Flow

#### 1. Get Course Details
```http
GET /course/{slug}
```

**Response:**
```json
{
    "course": {
        "id": 1,
        "name": "Laravel Advanced Course",
        "slug": "laravel-advanced-course",
        "price": 150000,
        "is_owned": false,
        "purchase_url": "/course/laravel-advanced-course/checkout"
    }
}
```

#### 2. Initiate Course Purchase
```http
POST /course/{course}/checkout
```

**Response:**
```json
{
    "snap_token": "abc123xyz",
    "transaction_id": "DC1234",
    "amount": 166500,
    "course": {
        "name": "Laravel Advanced Course",
        "price": 150000
    }
}
```

#### 3. Payment Notification (Webhook)
```http
POST /booking/payment/midtrans/notification
```

**Request Body:**
```json
{
    "transaction_status": "settlement",
    "order_id": "DC1234",
    "gross_amount": "166500.00",
    "custom_field1": "1",
    "custom_field2": "1",
    "custom_field3": "course"
}
```

### Course Access Validation

#### Check Course Ownership
```php
// Method: GET /api/course/{id}/access
{
    "has_access": true,
    "access_type": "purchased", // "purchased" | "free"
    "purchase_date": "2025-08-28T10:30:00Z",
    "transaction_id": "DC1234"
}
```

## Service Layer Architecture

### CourseService
```php
class CourseService {
    // Get courses available for purchase
    public function getCoursesForPurchase()
    
    // Get user's purchased courses
    public function getUserPurchasedCourses($userId)
    
    // Check if user owns a specific course
    public function userOwnsCourse($userId, $courseId)
}
```

### TransactionService
```php
class TransactionService {
    // Prepare course checkout data
    public function prepareCourseCheckout($courseId, $userId)
    
    // Get recent course purchases
    public function getRecentCourse($userId)
}
```

### PaymentService
```php
class PaymentService {
    // Create payment for course purchase
    public function createCoursePayment($courseId)
    
    // Handle payment notifications for courses
    protected function createCourseTransaction($notification, $course)
}
```

## Frontend Integration

### Course Cards
```blade
<x-course-card :course="$course" />

{{-- Shows purchase status and price --}}
@if(auth()->user()->hasPurchasedCourse($course->id))
    <span class="text-green-600">✓ Owned</span>
@else
    <div class="price">Rp {{ number_format($course->price) }}</div>
    <a href="/course/{{ $course->slug }}/checkout">Buy Now</a>
@endif
```

### Course Details Page
```blade
{{-- Course access section --}}
@auth
    @if(auth()->user()->hasPurchasedCourse($course->id))
        <a href="/course/{{ $course->slug }}" class="btn-primary">
            Continue Learning
        </a>
    @else
        <form id="purchase-form" data-course="{{ $course->slug }}">
            <button type="submit" class="btn-primary">
                Buy Course - Rp {{ number_format($course->price) }}
            </button>
        </form>
    @endif
@endauth
```

### Checkout Flow
```javascript
// Course purchase with Midtrans
const purchaseCourse = async (courseSlug) => {
    const response = await fetch(`/course/${courseSlug}/checkout`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    const data = await response.json();
    
    if (data.snap_token) {
        snap.pay(data.snap_token, {
            onSuccess: () => {
                window.location.href = `/course/${courseSlug}/success`;
            }
        });
    }
};
```

## Notification System

### Email Notifications
- **Template**: Course Purchase Confirmation
- **Trigger**: After successful payment
- **Content**: Course details, access link, lifetime access information

### WhatsApp Notifications
- **Template**: Course Purchase (course_purchase)
- **Variables**: 
  - `{user_name}`: Buyer's name
  - `{course_name}`: Course title
  - `{course_price}`: Course price
  - `{transaction_id}`: Transaction ID
  - `{course_url}`: Direct access link
  - `{dashboard_url}`: User dashboard

### Notification Flow
```php
// After successful payment
Mail::to($user->email)->send(new CoursePurchaseConfirmation($user, $course, $transaction));
$user->notify(new CoursePurchasedNotification($course, $transaction));
$whatsappService->sendCoursePurchaseNotification($transaction, $course);
```

## Admin Panel Features

### Course Management
- **Price Setting**: Set individual course prices
- **Sales Analytics**: View course revenue and purchase count
- **Access Management**: Manual course access grants

### Transaction Management
- **Course Transactions**: Filter by course purchases
- **Revenue Tracking**: Per-course revenue reports
- **Refund Management**: Handle course purchase refunds

## Migration Strategy

### Data Migration Script
```php
// Convert existing subscribers to course owners
php artisan migrate:subscription-to-courses

// Options:
--dry-run          // Preview changes without executing
--course-id        // Grant specific course to all subscribers
```

### Migration Logic
1. **Identify Active Subscribers**: Users with valid subscription transactions
2. **Grant Course Access**: Create course purchase transactions for all premium courses
3. **Preserve History**: Maintain original subscription records
4. **Update User Status**: Mark as migrated in user metadata

## Testing

### Unit Tests
```bash
# Test course purchase flow
php artisan test --filter=CoursePurchaseTest

# Test email notifications
php artisan test:course-email {user_id} {course_id}

# Test WhatsApp notifications
php artisan test:course-whatsapp {user_id} {course_id}
```

### Integration Tests
```bash
# Test full payment flow
php artisan test --filter=PaymentFlowTest

# Test course access middleware
php artisan test --filter=CourseAccessTest
```

## Security Considerations

### Access Control
- **Middleware**: `CheckCourseAccess` validates course ownership
- **Route Protection**: Premium course content requires authentication and ownership
- **Payment Verification**: Midtrans webhook signature validation

### Data Integrity
- **Transaction Atomicity**: Course purchases are atomic operations
- **Duplicate Prevention**: Unique transaction IDs prevent duplicate purchases
- **Audit Trail**: All purchase activities are logged

## Performance Optimizations

### Database Indexes
```sql
-- Optimize course ownership queries
ALTER TABLE transactions ADD INDEX idx_user_course_paid (user_id, course_id, is_paid);

-- Optimize course revenue queries
ALTER TABLE transactions ADD INDEX idx_course_revenue (course_id, is_paid, created_at);
```

### Caching Strategy
```php
// Cache user's purchased courses
Cache::remember("user.{$userId}.courses", 3600, function() use ($userId) {
    return User::find($userId)->purchasedCourses()->pluck('id')->toArray();
});
```

## Monitoring & Analytics

### Key Metrics
- **Course Sales**: Individual course purchase counts
- **Revenue per Course**: Track most profitable courses
- **User Engagement**: Course completion rates for purchased vs free courses
- **Conversion Rates**: Free course to paid course conversion

### Logging
```php
// Course purchase events
Log::info('Course purchased', [
    'user_id' => $userId,
    'course_id' => $courseId,
    'amount' => $amount,
    'transaction_id' => $transactionId
]);
```

## Troubleshooting

### Common Issues

#### 1. Payment Not Processing
- **Check**: Midtrans webhook configuration
- **Verify**: Transaction status in Midtrans dashboard
- **Debug**: Check Laravel logs for payment processing errors

#### 2. Course Access Denied
- **Verify**: User has completed payment
- **Check**: Transaction status is 'paid'
- **Debug**: Course ownership query results

#### 3. Email/WhatsApp Not Sent
- **Verify**: Notification service configuration
- **Check**: User email/phone number validity
- **Debug**: Queue job processing status

### Debug Commands
```bash
# Check course ownership
php artisan tinker
>>> User::find(1)->hasPurchasedCourse(1)

# Verify transaction status
>>> Transaction::where('booking_trx_id', 'DC1234')->first()->isActive()

# Test notification services
>>> app(WhatsappNotificationService::class)->testConnection()
```

## Deployment Checklist

### Pre-Deployment
- [ ] Run database migrations
- [ ] Seed WhatsApp message templates
- [ ] Configure Midtrans webhook URLs
- [ ] Update email templates
- [ ] Test payment flow in staging

### Post-Deployment
- [ ] Verify course purchase functionality
- [ ] Test notification delivery
- [ ] Monitor payment processing
- [ ] Check course access permissions
- [ ] Validate admin panel features

### Rollback Plan
- [ ] Database backup before migration
- [ ] Revert migration files if needed
- [ ] Restore previous payment routes if required

---

**Last Updated**: August 29, 2025  
**Version**: 2.0  
**Author**: Development Team