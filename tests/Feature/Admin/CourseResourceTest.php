<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\CourseResource;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
        
        // Create test category
        $this->category = Category::factory()->create();
        
        $this->actingAs($this->adminUser);
        
        // Fake storage for file uploads
        Storage::fake('public');
    }

    public function test_can_render_course_resource_list_page(): void
    {
        // Arrange
        Course::factory()->count(5)->create(['category_id' => $this->category->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->assertSuccessful();
    }

    public function test_can_list_courses(): void
    {
        // Arrange
        $courses = Course::factory()->count(3)->create(['category_id' => $this->category->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->assertCanSeeTableRecords($courses);
    }

    public function test_can_render_course_resource_create_page(): void
    {
        // Act & Assert
        Livewire::test(CourseResource\Pages\CreateCourse::class)
            ->assertSuccessful();
    }

    public function test_can_create_course(): void
    {
        // Arrange
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');
        $courseData = [
            'name' => 'Laravel Mastery Course',
            'thumbnail' => $thumbnail,
            'price' => 299000,
            'description' => 'Complete Laravel course for beginners to advanced',
            'is_popular' => true,
            'category_id' => $this->category->id,
            'benefits' => [
                'Learn Laravel fundamentals',
                'Build real-world applications',
                'Master advanced concepts'
            ],
        ];
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\CreateCourse::class)
            ->fillForm($courseData)
            ->call('create')
            ->assertHasNoFormErrors();
            
        $this->assertDatabaseHas('courses', [
            'name' => 'Laravel Mastery Course',
            'price' => 299000,
            'description' => 'Complete Laravel course for beginners to advanced',
            'is_popular' => true,
            'category_id' => $this->category->id,
        ]);
        
        // Assert file was uploaded
        $course = Course::where('name', 'Laravel Mastery Course')->first();
        $this->assertNotNull($course->thumbnail);
        Storage::disk('public')->assertExists($course->thumbnail);
    }

    public function test_can_validate_course_creation(): void
    {
        // Act & Assert
        Livewire::test(CourseResource\Pages\CreateCourse::class)
            ->fillForm([
                'name' => '',
                'price' => -100, // Invalid price
                'description' => '',
                'category_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'price' => 'min',
                'description' => 'required',
                'category_id' => 'required',
            ]);
    }

    public function test_can_render_course_resource_edit_page(): void
    {
        // Arrange
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\EditCourse::class, [
            'record' => $course->getRouteKey(),
        ])
            ->assertSuccessful();
    }

    public function test_can_retrieve_course_data_for_editing(): void
    {
        // Arrange
        $course = Course::factory()->create([
            'name' => 'React Fundamentals',
            'price' => 199000,
            'description' => 'Learn React from scratch',
            'category_id' => $this->category->id,
        ]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\EditCourse::class, [
            'record' => $course->getRouteKey(),
        ])
            ->assertFormSet([
                'name' => 'React Fundamentals',
                'price' => 199000,
                'description' => 'Learn React from scratch',
                'category_id' => $this->category->id,
            ]);
    }

    public function test_can_save_course_changes(): void
    {
        // Arrange
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        $newData = [
            'name' => 'Updated Course Name',
            'price' => 399000,
            'description' => 'Updated course description',
            'is_popular' => true,
        ];
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\EditCourse::class, [
            'record' => $course->getRouteKey(),
        ])
            ->fillForm($newData)
            ->call('save')
            ->assertHasNoFormErrors();
            
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated Course Name',
            'price' => 399000,
            'description' => 'Updated course description',
            'is_popular' => true,
        ]);
    }

    public function test_can_delete_course(): void
    {
        // Arrange
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\EditCourse::class, [
            'record' => $course->getRouteKey(),
        ])
            ->callAction(DeleteAction::class)
            ->assertSuccessful();
            
        $this->assertModelMissing($course);
    }

    public function test_can_search_courses(): void
    {
        // Arrange
        $course1 = Course::factory()->create([
            'name' => 'Laravel Advanced',
            'category_id' => $this->category->id
        ]);
        $course2 = Course::factory()->create([
            'name' => 'React Basics',
            'category_id' => $this->category->id
        ]);
        $course3 = Course::factory()->create([
            'name' => 'Vue.js Fundamentals',
            'category_id' => $this->category->id
        ]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->searchTable('Laravel')
            ->assertCanSeeTableRecords([$course1])
            ->assertCanNotSeeTableRecords([$course2, $course3]);
    }

    public function test_can_filter_courses_by_category(): void
    {
        // Arrange
        $category2 = Category::factory()->create();
        $course1 = Course::factory()->create(['category_id' => $this->category->id]);
        $course2 = Course::factory()->create(['category_id' => $category2->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->filterTable('category_id', $this->category->id)
            ->assertCanSeeTableRecords([$course1])
            ->assertCanNotSeeTableRecords([$course2]);
    }

    public function test_can_filter_courses_by_popularity(): void
    {
        // Arrange
        $popularCourse = Course::factory()->create([
            'is_popular' => true,
            'category_id' => $this->category->id
        ]);
        $regularCourse = Course::factory()->create([
            'is_popular' => false,
            'category_id' => $this->category->id
        ]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->filterTable('is_popular', true)
            ->assertCanSeeTableRecords([$popularCourse])
            ->assertCanNotSeeTableRecords([$regularCourse]);
    }

    public function test_can_bulk_delete_courses(): void
    {
        // Arrange
        $courses = Course::factory()->count(3)->create(['category_id' => $this->category->id]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->selectTableRecords($courses)
            ->callTableBulkAction(DeleteBulkAction::class)
            ->assertSuccessful();
            
        foreach ($courses as $course) {
            $this->assertModelMissing($course);
        }
    }

    public function test_can_sort_courses_by_price(): void
    {
        // Arrange
        $expensiveCourse = Course::factory()->create([
            'price' => 500000,
            'category_id' => $this->category->id
        ]);
        $cheapCourse = Course::factory()->create([
            'price' => 100000,
            'category_id' => $this->category->id
        ]);
        
        // Act & Assert
        Livewire::test(CourseResource\Pages\ListCourses::class)
            ->sortTable('price')
            ->assertCanSeeTableRecords([$cheapCourse, $expensiveCourse], inOrder: true);
    }

    public function test_benefits_field_handles_array_data(): void
    {
        // Arrange
        $benefits = [
            'Comprehensive curriculum',
            'Hands-on projects',
            'Expert instruction',
            'Certificate of completion'
        ];
        
        $courseData = [
            'name' => 'Full Stack Course',
            'price' => 599000,
            'description' => 'Complete full stack development course',
            'category_id' => $this->category->id,
            'benefits' => $benefits,
        ];
        
        // Act
        Livewire::test(CourseResource\Pages\CreateCourse::class)
            ->fillForm($courseData)
            ->call('create')
            ->assertHasNoFormErrors();
            
        // Assert
        $course = Course::where('name', 'Full Stack Course')->first();
        $this->assertEquals($benefits, $course->benefits);
    }
}