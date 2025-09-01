<?php

namespace App\Services;

use App\Models\Pricing;
use App\Models\Transaction;
use App\Models\Course;
use App\Models\Discount;
use App\Repositories\PricingRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\Services\DiscountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    protected $transactionRepository;
    protected $discountService;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        DiscountService $discountService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->discountService = $discountService;
    }


    /**
     * Prepare checkout for course purchase
     */
    public function prepareCourseCheckout(Course $course)
    {
        $user = Auth::user();
        $alreadyPurchased = $course->isPurchasedByUser($user->id);

        $admin_fee_amount = $course->admin_fee_amount ?? 0;
        $sub_total_amount = $course->price;
        
        // Check for applied discount in session
        $appliedDiscount = session()->get('applied_discount');
        $discount_amount = 0;
        
        if ($appliedDiscount) {
            $discount = Discount::find($appliedDiscount['id']);
            if ($discount && $discount->isValid($sub_total_amount)) {
                $discount_amount = $discount->calculateDiscount($sub_total_amount);
            } else {
                // Remove invalid discount from session
                session()->forget('applied_discount');
                session()->forget('discount_amount'); // Hapus juga discount_amount
                $appliedDiscount = null;
            }
        }
        
        $grand_total_amount = $sub_total_amount - $discount_amount + $admin_fee_amount;

        // For course purchases, no subscription dates needed
        $started_at = now();
        $ended_at = null; // Lifetime access

        // Save the selected course ID and admin fee into the session
        session()->put('course_id', $course->id);
        session()->put('admin_fee_amount', $admin_fee_amount);
        // HAPUS: session()->put('discount_amount', $discount_amount);
        // PaymentService akan menghitung sendiri discount_amount dari applied_discount
        session()->forget('pricing_id'); // Clear any existing pricing session

        return compact(
            'admin_fee_amount',
            'grand_total_amount',
            'sub_total_amount',
            'discount_amount',
            'course',
            'user',
            'alreadyPurchased',
            'started_at',
            'ended_at',
            'appliedDiscount'
        );
    }

    public function getRecentCourse()
    {
        $courseId = session()->get('course_id');
        return Course::find($courseId);
    }

    public function getUserTransactions()
    {
        $user = Auth::user();

        return $this->transactionRepository->getUserTransactions($user->id);
    }
    

    
    /**
     * Check if user has purchased a course
     */
    public function hasUserPurchasedCourse($courseId)
    {
        $user = Auth::user();
        
        return Transaction::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('is_paid', true)
            ->exists();
    }
    
    /**
     * Apply discount to session
     */
    public function applyDiscount(Discount $discount)
    {
        session()->put('applied_discount', [
            'id' => $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'type' => $discount->type,
            'value' => $discount->value,
            'maximum_discount' => $discount->maximum_discount,
            'applied_at' => now()->toISOString()
        ]);
        
        // Log untuk debugging
        Log::info('Discount applied to session', [
            'discount_id' => $discount->id,
            'discount_code' => $discount->code,
            'discount_type' => $discount->type,
            'discount_value' => $discount->value,
            'discount_maximum_discount' => $discount->maximum_discount
        ]);
    }
    
    /**
     * Remove discount from session
     */
    public function removeDiscount()
    {
        // Hapus semua session yang terkait dengan diskon
        session()->forget('applied_discount');
        session()->forget('discount_amount');
        
        // Log untuk debugging
        Log::info('Discount removed from session', [
            'remaining_session_keys' => array_keys(session()->all())
        ]);
    }
    
    /**
     * Get applied discount from session
     */
    public function getAppliedDiscount()
    {
        return session()->get('applied_discount');
    }
    
    /**
     * Calculate pricing with discount for course
     */
    public function calculatePricingWithDiscount(Course $course, Discount $discount = null)
    {
        $subtotal = $course->price;
        $admin_fee = $course->admin_fee_amount ?? 0;
        $discount_amount = 0;
        
        if ($discount && $discount->isValid($subtotal)) {
            $discount_amount = $discount->calculateDiscount($subtotal);
        }
        
        $grand_total = $subtotal - $discount_amount + $admin_fee;
        
        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discount_amount,
            'admin_fee' => $admin_fee,
            'grand_total' => $grand_total,
            'savings' => $discount_amount
        ];
    }
}
