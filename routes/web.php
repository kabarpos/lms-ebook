<?php

use App\Http\Controllers\Api\LessonProgressController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/pricing', [FrontController::class, 'pricing'])->name('front.pricing');
Route::get('/peraturan-layanan', [FrontController::class, 'termsOfService'])->name('front.terms-of-service');
Route::get('/course/{course:slug}', [FrontController::class, 'courseDetails'])->name('front.course.details');
Route::get('/course/{course:slug}/preview/{sectionContent}', [FrontController::class, 'previewContent'])->name('front.course.preview');

// Redirect old dashboard learning route to unified preview route
Route::get('/dashboard/learning/{course:slug}/{courseSection}/{sectionContent}', function(\App\Models\Course $course, $courseSection, \App\Models\SectionContent $sectionContent) {
    return redirect()->route('front.course.preview', ['course' => $course->slug, 'sectionContent' => $sectionContent->id]);
})->middleware(['auth'])->name('dashboard.course.learning');

Route::match(['get', 'post'], '/booking/payment/midtrans/notification',
[FrontController::class, 'paymentMidtransNotification'])
    ->name('front.payment_midtrans_notification');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:student|admin|super-admin'])->group(function () {
        Route::get('/dashboard/subscriptions/', [DashboardController::class, 'subscriptions'])
        ->name('dashboard.subscriptions');

        // model binding 1, 23, 412 ,2121
        Route::get('/dashboard/subscription/{transaction}', [DashboardController::class, 'subscription_details'])
        ->name('dashboard.subscription.details');

        Route::get('/dashboard/courses/', [CourseController::class, 'index'])
        ->name('dashboard');

        Route::get('/dashboard/search/courses', [CourseController::class, 'search_courses'])
        ->name('dashboard.search.courses');

        // Admin can access all learning content without subscription check
        Route::middleware(['check.subscription.or.admin'])->group(function () {
            Route::get('/dashboard/join/{course:slug}', [CourseController::class, 'join'])
            ->name('dashboard.course.join');



            Route::get('/dashboard/learning/{course:slug}/finished', [CourseController::class, 'learning_finished'])
            ->name('dashboard.course.learning.finished');
        });

        Route::get('/checkout/success', [FrontController::class, 'checkout_success'])
        ->name('front.checkout.success');

        Route::get('/checkout/{pricing}', [FrontController::class, 'checkout'])
        ->name('front.checkout');

        Route::post('/booking/payment/midtrans', [FrontController::class, 'paymentStoreMidtrans'])
        ->name('front.payment_store_midtrans');
    });

    // API Routes for Lesson Progress (JSON responses)
    Route::prefix('api')->middleware(['check.subscription.or.admin'])->group(function () {
        Route::get('/lesson-progress', [LessonProgressController::class, 'index'])
            ->name('api.lesson-progress.index');
        
        Route::post('/lesson-progress', [LessonProgressController::class, 'store'])
            ->name('api.lesson-progress.store');
        
        Route::get('/lesson-progress/{sectionContent}', [LessonProgressController::class, 'show'])
            ->name('api.lesson-progress.show');
        
        Route::put('/lesson-progress/{sectionContent}', [LessonProgressController::class, 'update'])
            ->name('api.lesson-progress.update');
        
        Route::get('/course-progress/{course}', [LessonProgressController::class, 'courseProgress'])
            ->name('api.course-progress');
    });


});

require __DIR__.'/auth.php';
