<?php

namespace App\Services;

use App\Models\Course;
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
        
        $completedLessons = $userProgress->where('is_completed', true)->count();
        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

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
            'progressPercentage' => $progressPercentage,
            'totalLessons' => $totalLessons,
            'completedLessons' => $completedLessons,
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
        return Cache::remember('featured_courses', 3600, function () use ($limit) {
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
}
