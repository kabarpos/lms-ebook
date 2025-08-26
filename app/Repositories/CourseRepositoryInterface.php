<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function searchByKeyword(string $keyword): Collection;

    public function getAllWithCategory(): Collection;

    public function getFeaturedCourses(int $limit = 6): Collection;
}
