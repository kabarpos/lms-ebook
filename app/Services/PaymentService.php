<?php

namespace App\Services;

use Exception;
use App\Helpers\TransactionHelper;
use App\Models\Course;
use App\Models\Discount;
use App\Models\PaymentTemp;
use App\Models\User;
use App\Mail\CoursePurchaseConfirmation;
use App\Notifications\CoursePurchasedNotification;
use App\Services\WhatsappNotificationService;
use App\Services\DiscountService;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    protected $midtransService;
    protected $pricingRepository;
    protected $transactionRepository;
    protected $whatsappService;
    protected $discountService;

    public function __construct(
        MidtransService $midtransService,
        TransactionRepositoryInterface $transactionRepository,
        WhatsappNotificationService $whatsappService,
        DiscountService $discountService
    )
    {
        $this->midtransService = $midtransService;
        $this->transactionRepository = $transactionRepository;
        $this->whatsappService = $whatsappService;
        $this->discountService = $discountService;
    }

    /**
     * Create payment for course purchase
     */
    public function createCoursePayment(int $courseId)
    {
        // ENHANCED LOGGING: Log awal proses payment
        Log::info('=== PAYMENT SERVICE START ===', [
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString()
        ]);
        
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        
        Log::info('Course and user loaded', [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'course_price' => $course->price,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);
        
        // Get discount from session if available
        $appliedDiscount = session()->get('applied_discount');
        $discountAmount = 0;
        $discountId = null;
        
        Log::info('Checking session for discount', [
            'session_applied_discount' => $appliedDiscount,
            'session_all_data' => session()->all()
        ]);
        
        if ($appliedDiscount) {
            Log::info('Discount found in session, calculating amount', [
                'discount_data' => $appliedDiscount,
                'course_price' => $course->price
            ]);
            
            $discountId = $appliedDiscount['id'] ?? null;
            
            // SELALU hitung ulang discount_amount dari applied_discount
            // JANGAN bergantung pada session discount_amount yang bisa tidak sinkron
            if (isset($appliedDiscount['type']) && isset($appliedDiscount['value'])) {
                if ($appliedDiscount['type'] === 'percentage') {
                    $discountAmount = ($course->price * $appliedDiscount['value']) / 100;
                    Log::info('Percentage discount calculated', [
                        'percentage' => $appliedDiscount['value'],
                        'calculated_amount' => $discountAmount
                    ]);
                    // Apply maximum discount limit if exists
                    if (isset($appliedDiscount['maximum_discount']) && $appliedDiscount['maximum_discount'] > 0) {
                        $originalAmount = $discountAmount;
                        $discountAmount = min($discountAmount, $appliedDiscount['maximum_discount']);
                        Log::info('Maximum discount limit applied', [
                            'original_amount' => $originalAmount,
                            'max_limit' => $appliedDiscount['maximum_discount'],
                            'final_amount' => $discountAmount
                        ]);
                    }
                } else {
                    $discountAmount = min($appliedDiscount['value'], $course->price);
                    Log::info('Fixed discount applied', [
                        'fixed_amount' => $discountAmount
                    ]);
                }
            }
            
            // Log untuk debugging
            Log::info('Final discount calculation', [
                'course_id' => $course->id,
                'course_price' => $course->price,
                'applied_discount' => $appliedDiscount,
                'calculated_discount_amount' => $discountAmount,
                'session_discount_amount' => session()->get('discount_amount', 'not_set'),
                'discount_id' => $discountId,
                'discount_type' => $appliedDiscount['type'],
                'discount_value' => $appliedDiscount['value'],
                'final_price' => $course->price - $discountAmount
            ]);
        } else {
            Log::warning('No discount found in session', [
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
                'course_id' => $courseId
            ]);
        }
        
        $adminFeeAmount = $course->admin_fee_amount ?? 0;
        $subTotal = $course->price;
        $grandTotal = $subTotal + $adminFeeAmount - $discountAmount;

        // Prepare item details with discount consideration
        $itemDetails = [
            [
                'id' => $course->id,
                'price' => (int) $course->price,
                'quantity' => 1,
                'name' => $course->name,
            ]
        ];
        
        // Add admin fee if exists
        if ($adminFeeAmount > 0) {
            $itemDetails[] = [
                'id' => 'admin_fee',
                'price' => (int) $adminFeeAmount,
                'quantity' => 1,
                'name' => 'Biaya Admin',
            ];
        }
        
        // Add discount as negative item if exists
        if ($discountAmount > 0) {
            $itemDetails[] = [
                'id' => 'discount',
                'price' => -(int) $discountAmount,
                'quantity' => 1,
                'name' => 'Diskon: ' . ($appliedDiscount['name'] ?? 'Diskon'),
            ];
        }

        // Prepare custom_expiry data
        $customExpiryData = [
            'admin_fee_amount' => $adminFeeAmount,
            'discount_amount' => $discountAmount,
            'discount_id' => $discountId
        ];
        
        Log::info('Preparing Midtrans parameters', [
            'order_id' => TransactionHelper::generateUniqueTrxId(),
            'gross_amount' => $grandTotal,
            'item_details' => $itemDetails,
            'custom_expiry_data' => $customExpiryData,
            'user_id' => $user->id,
            'course_id' => $courseId
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => TransactionHelper::generateUniqueTrxId(),
                'gross_amount' => (int) $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->whatsapp_number ?? '089998501293218'
            ],
            'item_details' => $itemDetails,
            'custom_field1' => $user->id,
            'custom_field2' => $courseId,
            'custom_field3' => 'course', // Mark as course purchase
            'custom_expiry' => json_encode($customExpiryData)
        ];
        
        Log::info('Final Midtrans parameters', [
            'params' => $params,
            'custom_expiry_json' => json_encode($customExpiryData)
        ]);

        $orderId = $params['transaction_details']['order_id'];
        $snapToken = $this->midtransService->createSnapToken($params);
        
        Log::info('Snap token created', [
            'order_id' => $orderId,
            'snap_token_length' => strlen($snapToken ?? ''),
            'success' => !empty($snapToken)
        ]);
        
        // Save payment data to temporary table for reliable access during webhook
        if ($snapToken) {
            try {
                PaymentTemp::createPaymentRecord([
                    'order_id' => $orderId,
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'sub_total_amount' => $subTotal,
                    'admin_fee_amount' => $adminFeeAmount,
                    'discount_amount' => $discountAmount,
                    'discount_id' => $discountId,
                    'grand_total_amount' => $grandTotal,
                    'snap_token' => $snapToken,
                    'discount_data' => $appliedDiscount
                ]);
                
                Log::info('Payment temp record created successfully', [
                    'order_id' => $orderId,
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'discount_amount' => $discountAmount,
                    'discount_id' => $discountId
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create payment temp record', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return $snapToken;
    }

    public function handlePaymentNotification()
    {
        Log::info('Processing Midtrans notification...');
        
        $notification = $this->midtransService->handleNotification();
        
        Log::info('Received Midtrans notification:', $notification);

        if (in_array($notification['transaction_status'], ['capture', 'settlement'])) {
            Log::info('Transaction status is valid for processing: ' . $notification['transaction_status']);
            
            // Only handle course purchases now
            $course = Course::findOrFail($notification['custom_field2']);
            Log::info('Found course:', ['id' => $course->id, 'name' => $course->name]);
            $result = $this->createCourseTransaction($notification, $course);
            
            // Send course purchase confirmation email
            if ($result) {
                $this->sendCoursePurchaseConfirmationEmail($result, $course);
            }
            
            Log::info('Transaction creation result:', ['success' => $result !== null]);
        } else {
            Log::warning('Transaction status not processed: ' . $notification['transaction_status']);
        }

        return $notification['transaction_status'];
    }

    /**
     * Create transaction for course purchase
     */
    protected function createCourseTransaction(array $notification, Course $course)
    {
        Log::info('Creating course transaction with data:', $notification);
        
        // Get admin fee amount from course model
        $adminFeeAmount = $course->admin_fee_amount ?? 0;
        
        // Try to get discount information from custom_expiry first
        $customExpiry = json_decode($notification['custom_expiry'] ?? '{}', true);
        $discountAmount = $customExpiry['discount_amount'] ?? 0;
        $discountId = $customExpiry['discount_id'] ?? null;
        
        // If custom_expiry is null or empty, fallback to payment_temp table
        if (empty($notification['custom_expiry']) || ($discountAmount == 0 && $discountId === null)) {
            $paymentTemp = PaymentTemp::findByOrderId($notification['order_id']);
            if ($paymentTemp) {
                $discountAmount = $paymentTemp->discount_amount ?? 0;
                $discountId = $paymentTemp->discount_id;
                $adminFeeAmount = $paymentTemp->admin_fee_amount ?? $adminFeeAmount;
                
                Log::info('=== USING PAYMENT_TEMP DATA (CUSTOM_EXPIRY FALLBACK) ===', [
                    'order_id' => $notification['order_id'],
                    'payment_temp_found' => true,
                    'discount_amount_from_temp' => $discountAmount,
                    'discount_id_from_temp' => $discountId,
                    'admin_fee_from_temp' => $adminFeeAmount,
                    'discount_data' => $paymentTemp->getDiscountInfo()
                ]);
            } else {
                Log::warning('=== NO PAYMENT_TEMP DATA FOUND ===', [
                    'order_id' => $notification['order_id'],
                    'custom_expiry_empty' => empty($notification['custom_expiry']),
                    'fallback_failed' => true
                ]);
            }
        }
        
        Log::info('=== FINAL DISCOUNT DATA FOR TRANSACTION ===', [
            'notification_order_id' => $notification['order_id'] ?? 'unknown',
            'raw_custom_expiry' => $notification['custom_expiry'] ?? 'null',
            'custom_expiry_parsed' => $customExpiry,
            'final_discount_amount' => $discountAmount,
            'final_discount_id' => $discountId,
            'data_source' => empty($notification['custom_expiry']) ? 'payment_temp' : 'custom_expiry'
        ]);
        
        $transactionData = [
            'user_id' => $notification['custom_field1'],
            'pricing_id' => null, // No pricing for course purchase
            'course_id' => $notification['custom_field2'],
            'sub_total_amount' => $course->price,
            'admin_fee_amount' => $adminFeeAmount,
            'discount_amount' => $discountAmount,
            'discount_id' => $discountId,
            'grand_total_amount' => $notification['gross_amount'],
            'payment_type' => 'Midtrans',
            'is_paid' => true,
            'booking_trx_id' => $notification['order_id'],
            'started_at' => now(),
            'ended_at' => null, // Course purchases have lifetime access
        ];
        
        Log::info('Course transaction data to be created:', $transactionData);

        try {
            $transaction = $this->transactionRepository->create($transactionData);
            
            Log::info('Course transaction successfully created:', [
                'id' => $transaction->id,
                'booking_trx_id' => $transaction->booking_trx_id,
                'user_id' => $transaction->user_id,
                'course_id' => $transaction->course_id
            ]);
            
            // Increment discount usage counter if discount was used
            if ($discountId) {
                try {
                    $discount = Discount::find($discountId);
                    if ($discount) {
                        $this->discountService->useDiscount($discount);
                        Log::info('Discount usage incremented successfully:', [
                            'discount_id' => $discountId,
                            'discount_code' => $discount->code,
                            'new_used_count' => $discount->fresh()->used_count,
                            'transaction_id' => $transaction->id
                        ]);
                    }
                } catch (\Exception $discountError) {
                    Log::warning('Failed to increment discount usage:', [
                        'discount_id' => $discountId,
                        'transaction_id' => $transaction->id,
                        'error' => $discountError->getMessage()
                    ]);
                }
            }
            
            // Clean up payment_temp record after successful transaction creation
            try {
                $paymentTemp = PaymentTemp::findByOrderId($notification['order_id']);
                if ($paymentTemp) {
                    $paymentTemp->delete();
                    Log::info('Payment temp record cleaned up', [
                        'order_id' => $notification['order_id'],
                        'payment_temp_id' => $paymentTemp->id
                    ]);
                }
            } catch (\Exception $cleanupError) {
                Log::warning('Failed to cleanup payment temp record', [
                    'order_id' => $notification['order_id'],
                    'error' => $cleanupError->getMessage()
                ]);
            }
            
            return $transaction;
        } catch (Exception $e) {
            Log::error('Failed to create course transaction:', [
                'error' => $e->getMessage(),
                'data' => $transactionData
            ]);
            throw $e;
        }
    }
    
    /**
     * Send course purchase confirmation email
     */
    protected function sendCoursePurchaseConfirmationEmail($transaction, Course $course)
    {
        try {
            $user = User::findOrFail($transaction->user_id);
            
            Log::info('Sending course purchase notifications', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'transaction_id' => $transaction->id
            ]);
            
            // Send custom email template
            Mail::to($user->email)->send(new CoursePurchaseConfirmation($user, $course, $transaction));
            
            // Also send notification (for database logging and potential future channels)
            $user->notify(new CoursePurchasedNotification($course, $transaction));
            
            // Send WhatsApp notification
            $this->whatsappService->sendCoursePurchaseNotification($transaction, $course);
            
            Log::info('Course purchase notifications sent successfully', [
                'email' => $user->email,
                'course' => $course->name
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to send course purchase notifications:', [
                'error' => $e->getMessage(),
                'user_id' => $transaction->user_id ?? null,
                'course_id' => $course->id
            ]);
            // Don't throw the exception as notification failure shouldn't break the payment flow
        }
    }

}
