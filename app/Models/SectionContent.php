<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'course_section_id',
        'content',
    ];

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    public function isCompletedByUser($userId)
    {
        return $this->userProgress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }
}
