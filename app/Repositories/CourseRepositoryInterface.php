<?php

namespace App\Repositories;

use App\Models\Discount;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface
{
    public function searchByKeyword(string $keyword): Collection;

    public function getAllWithCategory(): Collection;

    public function getFeaturedCourses(int $limit = 6): Collection;

    public function getCoursesWithFilters(array $filters = [], ?string $sort = null, int $perPage = 12): LengthAwarePaginator;
    
    public function getActiveDiscounts(): Collection;
    
    public function findDiscountByCode(string $code): ?Discount;
    
    public function getCoursesWithDiscounts(): Collection;
}
