<?php

namespace App\Services;

use Exception;
use App\Helpers\TransactionHelper;
use App\Models\Course;
use App\Models\User;
use App\Mail\CoursePurchaseConfirmation;
use App\Notifications\CoursePurchasedNotification;
use App\Services\WhatsappNotificationService;
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

    public function __construct(
        MidtransService $midtransService,
        TransactionRepositoryInterface $transactionRepository,
        WhatsappNotificationService $whatsappService
    )
    {
        $this->midtransService = $midtransService;
        $this->transactionRepository = $transactionRepository;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Create payment for course purchase
     */
    public function createCoursePayment(int $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        
        // Get discount from session if available
        $appliedDiscount = session()->get('applied_discount');
        $discountAmount = 0;
        $discountId = null;
        
        if ($appliedDiscount) {
            $discountId = $appliedDiscount['id'] ?? null;
            
            // SELALU hitung ulang discount_amount dari applied_discount
            // JANGAN bergantung pada session discount_amount yang bisa tidak sinkron
            if (isset($appliedDiscount['type']) && isset($appliedDiscount['value'])) {
                if ($appliedDiscount['type'] === 'percentage') {
                    $discountAmount = ($course->price * $appliedDiscount['value']) / 100;
                    // Apply maximum discount limit if exists
                    if (isset($appliedDiscount['maximum_discount']) && $appliedDiscount['maximum_discount'] > 0) {
                        $discountAmount = min($discountAmount, $appliedDiscount['maximum_discount']);
                    }
                } else {
                    $discountAmount = min($appliedDiscount['value'], $course->price);
                }
            }
            
            // Log untuk debugging
            Log::info('PaymentService discount calculation', [
                'course_id' => $course->id,
                'course_price' => $course->price,
                'applied_discount' => $appliedDiscount,
                'calculated_discount_amount' => $discountAmount,
                'session_discount_amount' => session()->get('discount_amount', 'not_set')
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
            'custom_expiry' => json_encode([
                'admin_fee_amount' => $adminFeeAmount,
                'discount_amount' => $discountAmount,
                'discount_id' => $discountId
            ])
        ];

        return $this->midtransService->createSnapToken($params);
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
        
        // Parse custom_expiry to get discount information
        $customExpiry = json_decode($notification['custom_expiry'] ?? '{}', true);
        $discountAmount = $customExpiry['discount_amount'] ?? 0;
        $discountId = $customExpiry['discount_id'] ?? null;
        
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
