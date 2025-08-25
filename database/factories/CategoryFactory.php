<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Web Development',
            'Mobile Development',
            'Data Science',
            'Machine Learning',
            'UI/UX Design',
            'Digital Marketing',
            'Cloud Computing',
            'Cybersecurity'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($categories),
        ];
    }
}