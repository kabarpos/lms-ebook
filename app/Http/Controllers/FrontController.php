<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\ErrorResponse;
use App\Helpers\SuccessResponse;
use App\Models\Course;
use App\Models\MidtransSetting;
use App\Services\CourseService;
use App\Services\PaymentService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FrontController extends Controller
{
    protected $transactionService;
    protected $paymentService;
    protected $courseService;

    public function __construct(
        PaymentService $paymentService,
        TransactionService $transactionService,
        CourseService $courseService
    ) {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
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

    public function courses()
    {
        // Get featured courses to display
        $featuredCourses = $this->courseService->getFeaturedCourses(12);
        $allCourses = $this->courseService->getCoursesForPurchase();
        $totalStudents = \App\Models\User::role('student')->count();
        $totalCourses = \App\Models\Course::count();
        
        return view('front.course-catalog', compact('featuredCourses', 'allCourses', 'totalStudents', 'totalCourses'));
    }

    public function termsOfService()
    {
        return view('front.terms-of-service');
    }

    public function courseDetails(\App\Models\Course $course)
    {
        $course->load(['category', 'courseSections.sectionContents', 'courseStudents', 'benefits']);
        $user = Auth::user();
        
        return view('front.course-details', compact('course', 'user'));
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
            // Check if user is authenticated and has course access
            if (!$user) {
                // Guest user trying to access premium content - show locked view
            } elseif (!$user->canAccessCourse($course->id)) {
                // Authenticated user without course access - redirect to course details for purchase
                return redirect()->route('front.course.details', $course->slug)
                    ->with('error', 'You need to purchase this course to access this content.');
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

    public function checkout_success()
    {
        // Check if recent course purchase
        $course = $this->transactionService->getRecentCourse();
        
        if ($course) {
            return view('front.course-checkout-success', compact('course'));
        }
        
        // No recent transaction found
        return redirect()->route('front.index')->with('error', 'No recent transaction found.');
    }
    
    /**
     * Course checkout page
     */
    public function courseCheckout(Course $course, Request $request)
    {
        try {
            Log::info('Checkout accessed', ['course_slug' => $course->slug, 'user_id' => Auth::id()]);
            
            // Check if user is authenticated
            if (!Auth::check()) {
                Log::info('User not authenticated, redirecting to login');
                return redirect()->route('login')
                    ->with('error', 'Please login to purchase this course.');
            }
            
            Log::info('Preparing checkout data for course', ['course_id' => $course->id]);
            $checkoutData = $this->transactionService->prepareCourseCheckout($course);
            Log::info('Checkout data prepared', $checkoutData);

            if ($checkoutData['alreadyPurchased']) {
                Log::info('User already purchased course, redirecting');
                return redirect()->route('front.course.details', $course->slug)
                    ->with('success', 'You already own this course!');
            }

            // Get Midtrans client key from database or fallback to config
            $midtransConfig = MidtransSetting::getActiveConfig();
            $clientKey = $midtransConfig && $midtransConfig->isValidConfig() 
                ? $midtransConfig->client_key 
                : config('midtrans.clientKey');
            
            $checkoutData['midtrans_client_key'] = $clientKey;
            Log::info('Client key added to checkout data', ['client_key_length' => strlen($clientKey ?? '')]);

            Log::info('Rendering checkout view with data', ['view' => 'front.course-checkout']);
            return view('front.course-checkout', $checkoutData);
            
        } catch (Exception $e) {
            Log::error('Checkout error', [
                'course_slug' => $course->slug ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a user-friendly error page or redirect
            return response()->view('errors.custom', [
                'message' => 'There was an error processing your checkout. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validate discount code for course
     */
    public function validateDiscount(Course $course, Request $request)
    {
        try {
            $request->validate([
                'discount_code' => 'required|string|max:50'
            ]);
            
            $discountCode = strtoupper(trim($request->discount_code));
            
            // Use DiscountService to validate discount
            $discountService = app(\App\Services\DiscountService::class);
            $validation = $discountService->validateDiscountForCourse($discountCode, $course);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 422);
            }
            
            // Apply discount to session using TransactionService
            $this->transactionService->applyDiscount($validation['discount']);
            
            // Calculate new totals using TransactionService
            $pricing = $this->transactionService->calculatePricingWithDiscount($course, $validation['discount']);
            
            return response()->json([
                'success' => true,
                'message' => $validation['message'],
                'discount' => [
                    'id' => $validation['discount']->id,
                    'name' => $validation['discount']->name,
                    'code' => $validation['discount']->code,
                    'type' => $validation['discount']->type,
                    'value' => $validation['discount']->value
                ],
                'pricing' => $pricing,
                'formatted' => [
                    'subtotal' => 'Rp ' . number_format($pricing['subtotal'], 0, ',', '.'),
                    'discount_amount' => 'Rp ' . number_format($pricing['discount_amount'], 0, ',', '.'),
                    'admin_fee' => 'Rp ' . number_format($pricing['admin_fee'], 0, ',', '.'),
                    'grand_total' => 'Rp ' . number_format($pricing['grand_total'], 0, ',', '.'),
                    'savings' => 'Rp ' . number_format($pricing['savings'], 0, ',', '.')
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            Log::warning('Discount validation failed', [
                'course_id' => $course->id,
                'discount_code' => $request->discount_code ?? 'N/A',
                'validation_errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kode diskon tidak boleh kosong atau tidak valid.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            Log::error('Discount validation error', [
                'course_id' => $course->id,
                'discount_code' => $request->discount_code ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clear any partial discount session data on error
            session()->forget(['applied_discount', 'discount_amount']);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.'
            ], 500);
        }
    }
    
    /**
     * Remove discount code from session
     */
    public function removeDiscount(Course $course, Request $request)
    {
        try {
            // Remove discount from session using TransactionService
            $this->transactionService->removeDiscount();
            
            // Recalculate pricing without discount
            $pricing = $this->transactionService->calculatePricingWithDiscount($course, null);
            
            return response()->json([
                'success' => true,
                'message' => 'Diskon berhasil dihapus.',
                'pricing' => $pricing,
                'formatted' => [
                    'subtotal' => 'Rp ' . number_format($pricing['subtotal'], 0, ',', '.'),
                    'discount_amount' => 'Rp ' . number_format($pricing['discount_amount'], 0, ',', '.'),
                    'admin_fee' => 'Rp ' . number_format($pricing['admin_fee'], 0, ',', '.'),
                    'grand_total' => 'Rp ' . number_format($pricing['grand_total'], 0, ',', '.'),
                    'savings' => 'Rp ' . number_format($pricing['savings'], 0, ',', '.')
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Remove discount error', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus diskon. Silakan coba lagi.'
            ], 500);
        }
    }
    
    /**
     * Handle course payment processing
     */
    public function paymentStoreCoursesMidtrans(Request $request)
    {
        try {
            // ENHANCED LOGGING: Log semua data request dan session
            Log::info('=== PAYMENT REQUEST RECEIVED ===', [
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'request_applied_discount' => $request->input('applied_discount'),
                'request_has_applied_discount' => $request->has('applied_discount'),
                'session_course_id' => session()->get('course_id'),
                'session_applied_discount' => session()->get('applied_discount'),
                'all_session_data' => session()->all(),
                'request_method' => $request->method(),
                'request_content_type' => $request->header('Content-Type'),
                'request_raw_body' => $request->getContent()
            ]);
            
            // Retrieve the course ID from request first, then fallback to session
            $courseId = $request->input('course_id') ?? session()->get('course_id');

            if (!$courseId) {
                Log::error('No course ID in request or session', [
                    'session_id' => session()->getId(),
                    'user_id' => Auth::id(),
                    'request_course_id' => $request->input('course_id'),
                    'session_course_id' => session()->get('course_id')
                ]);
                return response()->json(['error' => 'No course data found in the request or session.'], 400);
            }
            
            Log::info('Course ID resolved', [
                'course_id' => $courseId,
                'source' => $request->input('course_id') ? 'request' : 'session'
            ]);

            // Handle applied discount from frontend request
            $appliedDiscount = $request->input('applied_discount');
            Log::info('Processing discount from frontend', [
                'applied_discount_from_request' => $appliedDiscount,
                'session_discount_before' => session()->get('applied_discount')
            ]);
            
            if ($appliedDiscount) {
                // Validate and apply discount to session
                $discountService = app(\App\Services\DiscountService::class);
                $course = Course::findOrFail($courseId);
                
                $validation = $discountService->validateDiscountForCourse(
                    $appliedDiscount['code'], 
                    $course
                );
                
                if ($validation['valid']) {
                    $this->transactionService->applyDiscount($validation['discount']);
                    Log::info('Discount applied successfully', [
                        'discount_code' => $appliedDiscount['code'],
                        'session_discount_after' => session()->get('applied_discount')
                    ]);
                } else {
                    Log::warning('Invalid discount applied during payment', [
                        'discount_code' => $appliedDiscount['code'],
                        'course_id' => $courseId,
                        'validation_message' => $validation['message']
                    ]);
                }
            } else {
                Log::info('No discount in frontend request, checking session', [
                    'session_discount' => session()->get('applied_discount')
                ]);
            }

            // Log final session state before calling PaymentService
            Log::info('Final session state before PaymentService', [
                'course_id' => session()->get('course_id'),
                'applied_discount' => session()->get('applied_discount'),
                'session_id' => session()->getId()
            ]);

            // Call the PaymentService to generate the Snap token for course
            $snapToken = $this->paymentService->createCoursePayment($courseId);

            if (!$snapToken) {
                Log::error('Failed to create Midtrans transaction', [
                    'course_id' => $courseId,
                    'user_id' => Auth::id()
                ]);
                return response()->json(['error' => 'Failed to create Midtrans transaction.'], 500);
            }

            Log::info('Payment token created successfully', [
                'course_id' => $courseId,
                'user_id' => Auth::id(),
                'snap_token_length' => strlen($snapToken)
            ]);

            // Return the Snap token to the frontend
            return response()->json(['snap_token' => $snapToken], 200);
        } catch (Exception $e) {
            Log::error('Payment failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'course_id' => $courseId ?? null,
                'user_id' => Auth::id()
            ]);
            // Handle any exceptions that occur during transaction creation
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle Midtrans payment notification webhook
     */
    public function paymentMidtransNotification()
    {
        try {
            Log::info('Received Midtrans webhook notification');
            
            // Handle the payment notification
            $transactionStatus = $this->paymentService->handlePaymentNotification();
            
            Log::info('Payment notification processed', [
                'status' => $transactionStatus
            ]);
            
            return SuccessResponse::json(
                'Notification processed successfully',
                ['status' => $transactionStatus]
            );
            
        } catch (Exception $e) {
            return ErrorResponse::serverError(
                'Failed to process payment notification',
                $e
            );
        }
    }
}