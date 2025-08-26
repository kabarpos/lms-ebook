<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Course;
use App\Models\UserLessonProgress;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'occupation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function getActiveSubscription()
    {
        return $this->transactions()
            ->where('is_paid', true)
            ->where('ended_at', '>=', now())
            ->first(); // return details of subscription
    }

    public function hasActiveSubscription()
    {
        return $this->transactions()
            ->where('is_paid', true)
            ->where('ended_at', '>=', now()) // Ensure the subscription is still active
            ->exists(); // return boolean
    }

    public function lessonProgress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    public function completedLessons()
    {
        return $this->lessonProgress()->completed();
    }

    public function getCourseProgress($courseId)
    {
        $totalLessons = Course::find($courseId)
            ->courseSections
            ->sum(fn($section) => $section->sectionContents->count());
            
        $completedLessons = $this->lessonProgress()
            ->forCourse($courseId)
            ->completed()
            ->count();
            
        return [
            'total' => $totalLessons,
            'completed' => $completedLessons,
            'percentage' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0
        ];
    }
}
