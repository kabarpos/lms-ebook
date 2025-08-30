<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseRepository implements CourseRepositoryInterface
{
    public function searchByKeyword(string $keyword): Collection
    {
        return Course::where('name', 'like', "%{$keyword}%")
            ->orWhere('about', 'like', "%{$keyword}%")
            ->get();
    }

    public function getAllWithCategory(): Collection
    {
        return Course::with('category')->latest()->get();
    }

    public function getFeaturedCourses(int $limit = 6): Collection
    {
        return Course::with(['category', 'courseSections', 'courseStudents'])
            ->withCount('courseStudents')
            ->orderBy('course_students_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getCoursesWithFilters(array $filters = [], ?string $sort = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = Course::with(['category', 'courseMentors.mentor'])
            ->withCount(['courseStudents', 'courseSections']);

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['price_type'])) {
            if ($filters['price_type'] === 'free') {
                $query->where('price', 0);
            } elseif ($filters['price_type'] === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('about', 'like', "%{$filters['search']}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('course_students_count', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }
}
