<?php

namespace Database\Factories;

use App\Models\SectionContent;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SectionContent>
 */
class SectionContentFactory extends Factory
{
    protected $model = SectionContent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contentTypes = [
            'Video Tutorial',
            'Reading Material',
            'Code Exercise',
            'Quiz',
            'Assignment',
            'Live Demo',
            'Case Study',
            'Downloadable Resource'
        ];

        $content = $this->faker->paragraphs(2, true);
        
        // Add some sample video URL or content based on type
        if ($this->faker->boolean(60)) {
            $content .= "\n\nVideo URL: https://www.youtube.com/watch?v=example_video_id";
        }

        return [
            'name' => $this->faker->randomElement($contentTypes) . ': ' . $this->faker->sentence(3),
            'course_section_id' => CourseSection::factory(),
            'content' => $content,
        ];
    }
}