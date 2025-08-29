# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-08-29

### \ud83c\udf86 MAJOR BUSINESS MODEL TRANSFORMATION

**Complete removal of subscription-based model and transition to per-course purchase model**

### Added

#### Database & Models
- Added `price` column to `courses` table
- Added `course_id` column to `transactions` table for course-specific purchases
- Added course ownership methods to User model (`hasPurchasedCourse`, `purchasedCourses`, `canAccessCourse`)
- Added course purchase methods to Course model (`isPurchasedByUser`, `purchasedBy`, `transactions`)
- Added transaction type detection methods to Transaction model (`isCoursePurchase`, `isSubscription`)

#### Payment System
- New course checkout flow (`/course/{course}/checkout`)
- Course-specific payment processing with Midtrans
- Course purchase success pages

#### Notification System
- Email notifications for course purchases (`CoursePurchaseConfirmation` mail class)
- WhatsApp notifications for course purchases with new template type
- Multi-channel notification support (email + WhatsApp + database)
- Course purchase confirmation templates

#### Admin Panel Features
- Course pricing management in Filament admin
- Course sales analytics and revenue tracking
- Transaction filtering by type (course only)
- Enhanced course and transaction resources

#### Frontend Components
- Updated course cards with pricing and purchase status
- New course detail pages with purchase interface
- Course checkout flow with payment integration
- User dashboard showing purchased courses
- Course access middleware for ownership validation

#### Services & Architecture
- `CourseService` enhancements for purchase management
- `TransactionService` updates for course transactions
- `PaymentService` course payment methods
- `WhatsappNotificationService` course purchase notifications
- `CheckCourseAccess` middleware for course ownership validation

#### Migration & Analysis Tools
- Data migration command (`MigrateSubscriptionToCoursesCommand`) ✅ **IMPLEMENTED**
- Subscription data analysis command (`AnalyzeSubscriptionDataCommand`) ✅ **IMPLEMENTED**
- Dry-run capability for safe migration testing ✅ **TESTED**
- Batch processing for large datasets ✅ **IMPLEMENTED**
- CSV export for detailed analysis ✅ **IMPLEMENTED**
- Progress tracking and error handling ✅ **IMPLEMENTED**

#### Testing & Development
- Course email testing command (`php artisan test:course-email`) ✅ **TESTED & WORKING**
- Course WhatsApp testing command (`php artisan test:course-whatsapp`) ✅ **TESTED & WORKING**
- Subscription data analysis command (`php artisan analyze:subscription-data`) ✅ **IMPLEMENTED & TESTED**
- Migration command with dry-run capability ✅ **TESTED & WORKING**
- Comprehensive testing infrastructure ✅ **COMPLETE**

### Changed

#### Business Logic
- **BREAKING**: Completely removed subscription model in favor of individual course purchases
- Course access now based on individual purchase
- User authentication flow updated to support course ownership
- Payment processing logic updated for course transactions only

#### User Interface
- Course catalog now displays individual prices
- Course detail pages show purchase options
- Navigation updated to remove pricing/subscription references
- Dashboard redesigned to show owned courses

#### Backend Architecture
- Transaction model updated to handle course purchases only
- Course model enhanced with pricing and ownership features
- Payment webhook handling updated for course transactions
- Email and WhatsApp templates updated for course purchases

### Removed

#### Legacy Features (Completely Removed)
- Subscription-based pricing system
- Legacy subscription transactions
- Old pricing management interface
- Subscription-related middleware (`CheckSubscription`, `CheckSubscriptionOrAdmin`)
- Pricing model and repository
- Subscription routes and controllers
- Pricing-related factories and seeders

### Migration Path

#### Data Migration
- Migration script executed for converting subscription users to course ownership
- All existing subscribers converted to course owners with lifetime access
- Audit trail preserved for all existing transactions
- Pricing table and related data completely removed

### Technical Details

#### Database Schema Changes
```sql
-- Courses table enhancement
ALTER TABLE courses ADD COLUMN price UNSIGNED INTEGER DEFAULT 0 AFTER is_popular;

-- Transactions table enhancement  
ALTER TABLE transactions ADD COLUMN course_id FOREIGN KEY NULLABLE AFTER pricing_id;

-- Remove pricing relationship
ALTER TABLE transactions DROP COLUMN pricing_id;
```

#### Removed Routes
- `GET /dashboard/subscriptions` - Subscription dashboard
- `GET /dashboard/subscription/{transaction}` - Subscription details
- `GET /checkout/{pricing}` - Subscription checkout
- `POST /booking/payment/midtrans` - Subscription payment processing

#### New Routes
- `GET /course/{course}` - Course detail with purchase option
- `POST /course/{course}/checkout` - Initiate course purchase
- `GET /course/{course}/success` - Purchase success page
- `POST /booking/payment/midtrans/notification` - Enhanced webhook handler

#### New Middleware
- `CheckCourseAccess` - Validates course ownership for premium content

#### New Mail Classes
- `CoursePurchaseConfirmation` - Course purchase email notification

#### New Notification Classes
- `CoursePurchasedNotification` - Multi-channel course purchase notification

#### Enhanced Services
- `PaymentService::createCoursePayment()` - Course-specific payment creation
- `PaymentService::createCourseTransaction()` - Course transaction handling
- `CourseService::getUserPurchasedCourses()` - User course ownership queries
- `WhatsappNotificationService::sendCoursePurchaseNotification()` - Course WhatsApp notifications

### Security Enhancements

- Course access validation middleware
- Payment verification for course transactions
- Secure course ownership checks
- Transaction integrity validation

### Performance Improvements

- Optimized course ownership queries
- Efficient transaction type detection
- Cached user course relationships
- Improved payment processing performance

### Documentation

- Comprehensive system documentation (`PER_COURSE_PURCHASE_DOCUMENTATION.md`)
- Updated README with new business model information
- API documentation for course purchase endpoints
- Migration and deployment guides

---

## [1.0.0] - 2025-08-27

### Initial Release

#### Added
- Basic LMS functionality with subscription model
- Filament admin panel
- Midtrans payment integration
- WhatsApp notification system
- Course and user management
- Basic email notifications

---

**Legend:**
- \ud83c\udf86 Major release
- \u26a1 Breaking changes
- \u2728 New features
- \ud83d\udc1b Bug fixes
- \ud83d\udcc4 Documentation updates
- \ud83d\udd27 Maintenance updates