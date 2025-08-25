<?php

namespace Database\Seeders;

use App\Models\Pricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pricingPlans = [
            [
                'name' => 'Basic Plan',
                'duration' => 1,
                'price' => 99000,
            ],
            [
                'name' => 'Premium Plan',
                'duration' => 3,
                'price' => 249000,
            ],
            [
                'name' => 'Pro Plan',
                'duration' => 6,
                'price' => 449000,
            ],
            [
                'name' => 'Ultimate Plan',
                'duration' => 12,
                'price' => 799000,
            ],
        ];

        foreach ($pricingPlans as $plan) {
            Pricing::create($plan);
        }
    }
}