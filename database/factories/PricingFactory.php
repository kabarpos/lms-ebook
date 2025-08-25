<?php

namespace Database\Factories;

use App\Models\Pricing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pricing>
 */
class PricingFactory extends Factory
{
    protected $model = Pricing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pricingPlans = [
            ['name' => 'Basic Plan', 'duration' => 1, 'price' => 99000],
            ['name' => 'Premium Plan', 'duration' => 3, 'price' => 249000],
            ['name' => 'Pro Plan', 'duration' => 6, 'price' => 449000],
            ['name' => 'Ultimate Plan', 'duration' => 12, 'price' => 799000],
        ];

        $plan = $this->faker->randomElement($pricingPlans);

        return [
            'name' => $plan['name'],
            'duration' => $plan['duration'],
            'price' => $plan['price'],
        ];
    }
}