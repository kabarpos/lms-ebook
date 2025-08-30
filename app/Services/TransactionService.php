<?php

namespace App\Services;

use App\Models\Pricing;
use App\Models\Transaction;
use App\Models\Course;
use App\Repositories\PricingRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $transactionRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->transactionRepository = $transactionRepository;
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
        $grand_total_amount = $sub_total_amount + $admin_fee_amount;

        // For course purchases, no subscription dates needed
        $started_at = now();
        $ended_at = null; // Lifetime access

        // Save the selected course ID and admin fee into the session
        session()->put('course_id', $course->id);
        session()->put('admin_fee_amount', $admin_fee_amount);
        session()->forget('pricing_id'); // Clear any existing pricing session

        return compact(
            'admin_fee_amount',
            'grand_total_amount',
            'sub_total_amount',
            'course',
            'user',
            'alreadyPurchased',
            'started_at',
            'ended_at'
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

        // if (!$user) {
        //     return collect(); // Return an empty collection if the user is not authenticated
        // }

        return $this->transactionRepository->getUserTransactions($user->id);

        // n+1 query
        // return Transaction::with('pricing') // Assuming `Transaction` has a `pricing` relationship
        //     ->where('user_id', $user->id)
        //     ->orderBy('created_at', 'desc')
        //     ->get();
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
}
