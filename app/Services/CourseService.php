<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Discount;
use App\Models\UserLessonProgress;
use App\Repositories\CourseRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function enrollUser(Course $course)
    {
        $user = Auth::user();

        // Check if user is already enrolled
        if (!$course->courseStudents()->where('user_id', $user->id)->exists()) {
            $course->courseStudents()->create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        }

        return $user->name;
    }

    public function getFirstSectionAndContent(Course $course)
    {
        $firstSectionId = $course->courseSections()->orderBy('id')->value('id');
        $firstContentId = $firstSectionId
            ? $course->courseSections()->find($firstSectionId)->sectionContents()->orderBy('id')->value('id')
            : null;

        return [
            'firstSectionId' => $firstSectionId,
            'firstContentId' => $firstContentId,
        ];
    }

    public function getLearningData(Course $course, $contentSectionId, $sectionContentId)
    {
        $course->load(['courseSections.sectionContents']);
        $user = Auth::user();

        $currentSection = $course->courseSections->find($contentSectionId);
        $currentContent = $currentSection ? $currentSection->sectionContents->find($sectionContentId) : null;

        // Get user progress for this course
        $userProgress = UserLessonProgress::forUser($user->id)
            ->forCourse($course->id)
            ->get()
            ->keyBy('section_content_id');

        // Calculate progress statistics
        $totalLessons = $course->courseSections->sum(function ($section) {
            return $section->sectionContents->count();
        });
        
        $completedLessonsCount = $userProgress->where('is_completed', true)->count();
        $completedLessons = $userProgress->where('is_completed', true)->pluck('section_content_id')->toArray();
        $progressPercentage = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100, 2) : 0;

        // Check if current lesson is completed
        $isCurrentCompleted = $currentContent && isset($userProgress[$currentContent->id]) 
            ? $userProgress[$currentContent->id]->is_completed 
            : false;

        // Determine next and previous content
        $nextContent = null;
        $prevContent = null;

        if ($currentContent) {
            // Find next content in current section
            $nextContent = $currentSection->sectionContents
                ->where('id', '>', $currentContent->id)
                ->sortBy('id')
                ->first();
                
            // Find previous content in current section
            $prevContent = $currentSection->sectionContents
                ->where('id', '<', $currentContent->id)
                ->sortByDesc('id')
                ->first();
        }

        // If no next content in current section, find first content of next section
        if (!$nextContent && $currentSection) {
            $nextSection = $course->courseSections
                ->where('id', '>', $currentSection->id)
                ->sortBy('id')
                ->first();

            if ($nextSection) {
                $nextContent = $nextSection->sectionContents->sortBy('id')->first();
            }
        }

        // If no previous content in current section, find last content of previous section
        if (!$prevContent && $currentSection) {
            $prevSection = $course->courseSections
                ->where('id', '<', $currentSection->id)
                ->sortByDesc('id')
                ->first();

            if ($prevSection) {
                $prevContent = $prevSection->sectionContents->sortByDesc('id')->first();
            }
        }

        return [
            'course' => $course,
            'currentSection' => $currentSection,
            'currentContent' => $currentContent,
            'nextContent' => $nextContent,
            'prevContent' => $prevContent,
            'isFinished' => !$nextContent,
            'currentProgress' => $progressPercentage,
            'progressPercentage' => $progressPercentage,
            'totalLessons' => $totalLessons,
            'completedLessons' => $completedLessons,
            'completedLessonsCount' => $completedLessonsCount,
            'isCompleted' => $isCurrentCompleted,
            'isCurrentCompleted' => $isCurrentCompleted,
            'userProgress' => $userProgress
        ];
    }

    public function searchCourses(string $keyword)
    {
        return $this->courseRepository->searchByKeyword($keyword);
    }

    public function getCoursesGroupedByCategory()
    {
        // Use caching for better performance (cache for 1 hour)
        return Cache::remember('courses_by_category', 3600, function () {
            $courses = $this->courseRepository->getAllWithCategory();
            
            return $courses->groupBy(function ($course) {
                return $course->category->name ?? 'Uncategorized';
            });
        });
    }

    public function getFeaturedCourses($limit = 6)
    {
        return Cache::remember("featured_courses_{$limit}", 3600, function () use ($limit) {
            return $this->courseRepository->getFeaturedCourses($limit);
        });
    }

    public function getPopularCourses($limit = 6)
    {
        return Cache::remember('popular_courses', 3600, function () use ($limit) {
            return Course::withCount('courseStudents')
                ->orderBy('course_students_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }
    
    /**
     * Get courses available for purchase with pricing
     */
    public function getCoursesForPurchase()
    {
        return Course::where('price', '>', 0)
            ->with(['category', 'courseMentors.mentor'])
            ->withCount(['courseStudents', 'courseSections'])
            ->orderBy('is_popular', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get user's purchased courses
     */
    public function getUserPurchasedCourses()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }
        
        return $user->purchasedCourses()
            ->with(['category', 'courseMentors.mentor'])
            ->withCount(['courseSections'])
            ->get();
    }
    
    /**
     * Check if user can access course content
     */
    public function canUserAccessCourse(Course $course)
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->canAccessCourse($course->id);
    }
    
    /**
     * Get course with purchase status for user
     */
    public function getCourseWithPurchaseStatus(Course $course)
    {
        $user = Auth::user();
        
        $course->load([
            'category',
            'benefits',
            'courseSections.sectionContents',
            'courseMentors.mentor'
        ]);
        
        $course->is_purchased = $user ? $user->hasPurchasedCourse($course->id) : false;
        $course->can_access = $user ? $user->canAccessCourse($course->id) : false;
        
        return $course;
    }
    
    /**
     * Get courses purchased by the authenticated user grouped by category
     */
    public function getPurchasedCoursesGroupedByCategory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }
        
        // Get purchased courses - both via individual purchase and subscription
        $purchasedCourses = Course::whereHas('transactions', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('is_paid', true);
        })->orWhereHas('courseStudents', function($query) use ($user) {
            // Legacy: users who are already enrolled
            $query->where('user_id', $user->id)
                  ->where('is_active', true);
        })->with('category')
          ->get();
        
        return $purchasedCourses->groupBy(function ($course) {
            return $course->category->name ?? 'Uncategorized';
        });
    }
    
    /**
     * Apply discount to course and calculate final price
     */
    public function applyDiscountToCourse(Course $course, string $discountCode = null)
    {
        $originalPrice = $course->price;
        $finalPrice = $originalPrice;
        $discount = null;
        $discountAmount = 0;
        
        if ($discountCode) {
            $discount = Discount::where('code', $discountCode)
                ->active()
                ->available()
                ->first();
                
            if ($discount && $discount->isValid($originalPrice)) {
                $discountAmount = $discount->calculateDiscount($originalPrice);
                $finalPrice = max(0, $originalPrice - $discountAmount);
            }
        }
        
        return [
            'original_price' => $originalPrice,
            'final_price' => $finalPrice,
            'discount_amount' => $discountAmount,
            'discount' => $discount,
            'savings' => $originalPrice - $finalPrice
        ];
    }
    
    /**
     * Validate discount code for a course
     */
    public function validateDiscountCode(string $discountCode, Course $course)
    {
        $discount = Discount::where('code', $discountCode)
            ->active()
            ->available()
            ->first();
            
        if (!$discount) {
            return [
                'valid' => false,
                'message' => 'Kode diskon tidak valid atau sudah tidak aktif.'
            ];
        }
        
        if (!$discount->isValid($course->price)) {
            $reasons = [];
            
            if ($discount->minimum_amount && $course->price < $discount->minimum_amount) {
                $reasons[] = 'Minimum pembelian Rp ' . number_format($discount->minimum_amount, 0, '', '.');
            }
            
            if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
                $reasons[] = 'Kuota penggunaan sudah habis';
            }
            
            if ($discount->start_date && now() < $discount->start_date) {
                $reasons[] = 'Diskon belum berlaku';
            }
            
            if ($discount->end_date && now() > $discount->end_date) {
                $reasons[] = 'Diskon sudah berakhir';
            }
            
            return [
                'valid' => false,
                'message' => 'Kode diskon tidak dapat digunakan: ' . implode(', ', $reasons)
            ];
        }
        
        $discountAmount = $discount->calculateDiscount($course->price);
        
        return [
            'valid' => true,
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'final_price' => max(0, $course->price - $discountAmount),
            'message' => 'Kode diskon berhasil diterapkan!'
        ];
    }
}
