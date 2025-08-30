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
        
        $adminFeeAmount = $course->admin_fee_amount ?? 0;
        $grandTotal = $course->price + $adminFeeAmount;

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
            'item_details' => array_filter([
                [
                    'id' => $course->id,
                    'price' => (int) $course->price,
                    'quantity' => 1,
                    'name' => $course->name,
                ],
                $adminFeeAmount > 0 ? [
                    'id' => 'admin_fee',
                    'price' => (int) $adminFeeAmount,
                    'quantity' => 1,
                    'name' => 'Biaya Admin',
                ] : null,
            ]),
            'custom_field1' => $user->id,
            'custom_field2' => $courseId,
            'custom_field3' => 'course', // Mark as course purchase
            'custom_expiry' => json_encode(['admin_fee_amount' => $adminFeeAmount])
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
        
        $transactionData = [
            'user_id' => $notification['custom_field1'],
            'pricing_id' => null, // No pricing for course purchase
            'course_id' => $notification['custom_field2'],
            'sub_total_amount' => $course->price,
            'admin_fee_amount' => $adminFeeAmount,
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
