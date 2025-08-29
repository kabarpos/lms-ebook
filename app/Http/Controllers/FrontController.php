<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
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

    public function pricing()
    {
        // Redirect to course browsing instead of subscription packages
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
    public function courseCheckout(Course $course)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to purchase this course.');
        }
        
        $checkoutData = $this->transactionService->prepareCourseCheckout($course);

        if ($checkoutData['alreadyPurchased']) {
            return redirect()->route('front.course.details', $course->slug)
                ->with('success', 'You already own this course!');
        }

        return view('front.course-checkout', $checkoutData);
    }
    
    /**
     * Handle course payment processing
     */
    public function paymentStoreCoursesMidtrans()
    {
        try {
            // Retrieve the course ID from the session
            $courseId = session()->get('course_id');

            if (!$courseId) {
                return response()->json(['error' => 'No course data found in the session.'], 400);
            }

            // Call the PaymentService to generate the Snap token for course
            $snapToken = $this->paymentService->createCoursePayment($courseId);

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
}