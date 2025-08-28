<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pricing;
use App\Services\CourseService;
use App\Services\PaymentService;
use App\Services\PricingService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FrontController extends Controller
{
    protected $transactionService;
    protected $paymentService;
    protected $pricingService;
    protected $courseService;

    public function __construct(
        PaymentService $paymentService,
        TransactionService $transactionService,
        PricingService $pricingService,
        CourseService $courseService
    ) {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
        $this->pricingService = $pricingService;
        $this->courseService = $courseService;
    }

    //
    public function index()
    {
        // Get featured courses to display on homepage
        $featuredCourses = $this->courseService->getFeaturedCourses(6);
        $totalStudents = \App\Models\User::role('student')->count();
        $totalCourses = \App\Models\Course::count();
        
        return view('front.index', compact('featuredCourses', 'totalStudents', 'totalCourses'));
    }

    public function pricing()
    {
        $pricing_packages = $this->pricingService->getAllPackages();
        $user = Auth::user();
        $totalCourses = \App\Models\Course::count();
        $totalStudents = \App\Models\User::role('student')->count();
        
        return view('front.pricing', compact('pricing_packages', 'user', 'totalCourses', 'totalStudents'));
    }

    public function termsOfService()
    {
        return view('front.terms-of-service');
    }

    public function courseDetails(\App\Models\Course $course)
    {
        $course->load(['category', 'courseSections.sectionContents', 'courseStudents', 'benefits']);
        $user = Auth::user();
        $pricing_packages = $this->pricingService->getAllPackages();
        
        return view('front.course-details', compact('course', 'user', 'pricing_packages'));
    }

    public function previewContent(
        \App\Models\Course $course, 
        $courseSectionOrSectionContent, 
        \App\Models\SectionContent $sectionContent = null
    ) {
        // Handle both route patterns: preview/{sectionContent} and learning/{courseSection}/{sectionContent}
        if ($sectionContent === null) {
            // Preview route: course/{course}/preview/{sectionContent}
            $sectionContent = $courseSectionOrSectionContent;
        } else {
            // Legacy learning route redirect handled in routes
            $sectionContent = $sectionContent;
        }

        // Check if the content belongs to the course
        if ($sectionContent->courseSection->course_id !== $course->id) {
            abort(404, 'Content not found in this course.');
        }

        // UNIFIED ACCESS CONTROL: Check if user can access premium content
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('admin') || $user->hasRole('super-admin'));
        
        // Ensure $isAdmin is always a boolean
        $isAdmin = (bool) $isAdmin;
        
        // For premium content, check access rights
        if (!$sectionContent->is_free && !$isAdmin) {
            // Check if user is authenticated and has subscription
            if (!$user) {
                // Guest user trying to access premium content - show locked view
            } elseif (!$user->hasActiveSubscription()) {
                // Authenticated user without subscription - redirect to pricing
                return redirect()->route('front.pricing')
                    ->with('error', 'You need an active subscription to access this premium content.');
            }
        }

        $course->load(['category', 'courseSections.sectionContents', 'courseStudents', 'benefits']);
        $currentSection = $sectionContent->courseSection;
        
        // Prepare base data
        $viewData = compact('course', 'currentSection', 'sectionContent', 'isAdmin');
        
        // Add learning data for authenticated users (including admin)
        if ($user) {
            $learningData = $this->courseService->getLearningData(
                $course, 
                $currentSection->id, 
                $sectionContent->id
            );
            
            // Merge learning data with existing view data
            $viewData = array_merge($viewData, $learningData);
        }
        
        return view('front.course-preview', $viewData);
    }

    public function checkout(Pricing $pricing)
    {
        $checkoutData = $this->transactionService->prepareCheckout($pricing);

        if ($checkoutData['alreadySubscribed']) {
            return redirect()->route('front.pricing')->with('error', 'You are already subscribed to this plan.');
        }

        return view('front.checkout', $checkoutData);
    }

    public function paymentStoreMidtrans()
    {
        try {
            // Retrieve the pricing ID from the session
            $pricingId = session()->get('pricing_id');

            if (!$pricingId) {
                return response()->json(['error' => 'No pricing data found in the session.'], 400);
            }

            // Call the PaymentService to generate the Snap token
            $snapToken = $this->paymentService->createPayment($pricingId);

            if (!$snapToken) {
                return response()->json(['error' => 'Failed to create Midtrans transaction.'], 500);
            }

            // Return the Snap token to the frontend
            return response()->json(['snap_token' => $snapToken], 200);
        } catch (Exception $e) {
            // Handle any exceptions that occur during transaction creation
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function paymentMidtransNotification(Request $request)
    {
        // Log all incoming webhook data for debugging
        Log::info('Midtrans webhook received:', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);
        
        try {
            // Process the Midtrans notification through the service
            $transactionStatus = $this->paymentService->handlePaymentNotification();

            if (!$transactionStatus) {
                Log::error('Invalid notification data received');
                return response()->json(['error' => 'Invalid notification data.'], 400);
            }

            Log::info('Webhook processed successfully:', ['status' => $transactionStatus]);
            
            // transaction has been created in database
            return response()->json(['status' => $transactionStatus]);
        } catch (Exception $e) {
            Log::error('Failed to handle Midtrans notification:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to process notification.'], 500);
        }
    }

    public function checkout_success()
    {
        $pricing = $this->transactionService->getRecentPricing();

        if (!$pricing) {
            return redirect()->route('front.pricing')->with('error', 'No recent subscription found.');
        }

        return view('front.checkout_success', compact('pricing'));
    }
}
