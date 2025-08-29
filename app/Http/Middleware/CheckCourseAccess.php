<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You need to login to access courses.');
        }
        
        // Get course from route parameter
        $course = $request->route('course');
        
        // If course parameter is not found, continue (might be course list page)
        if (!$course) {
            return $next($request);
        }
        
        // If course is a slug, find the course
        if (is_string($course)) {
            $course = Course::where('slug', $course)->first();
        }
        
        // If course not found, return 404
        if (!$course) {
            abort(404, 'Course not found.');
        }
        
        // Check if user can access the course
        if (!$user->canAccessCourse($course->id)) {
            // Redirect to course detail page with purchase option
            return redirect()->route('front.course.details', $course->slug)
                ->with('error', 'You need to purchase this course to access its content.');
        }
        
        return $next($request);
    }
}
