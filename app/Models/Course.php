<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'thumbnail',
        'about',
        'is_popular',
        'category_id',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(CourseBenefit::class);
    }

    public function courseSections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'course_id');
    }

    public function courseStudents(): HasMany
    {
        return $this->hasMany(CourseStudent::class, 'course_id');
    }

    public function courseMentors(): HasMany
    {
        return $this->hasMany(CourseMentor::class, 'course_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getContentCountAttribute()
    {
        // Use loadCount to avoid N+1 queries
        if (!$this->relationLoaded('courseSections')) {
            $this->loadCount(['courseSections' => function ($query) {
                $query->withCount('sectionContents');
            }]);
        }
        
        return $this->courseSections->sum('section_contents_count');
    }

    // Add scope for eager loading common relationships
    public function scopeWithFullDetails($query)
    {
        return $query->with([
            'category',
            'benefits',
            'courseSections.sectionContents',
            'courseMentors.mentor'
        ]);
    }

    // Add scope for listing with counts
    public function scopeWithCounts($query)
    {
        return $query->withCount([
            'courseSections',
            'courseStudents',
            'courseMentors'
        ]);
    }

    // Add accessor for better performance
    public function getStudentCountAttribute()
    {
        return $this->course_students_count ?? $this->courseStudents()->count();
    }

    // Add accessor for section count
    public function getSectionCountAttribute()
    {
        return $this->course_sections_count ?? $this->courseSections()->count();
    }
}
