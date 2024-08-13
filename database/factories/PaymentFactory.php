<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groupCount = Group::all()->count();
        return [
            'label' => fake()->words(2, true),
            'group_id' => fake()->numberBetween(1, $groupCount),
            'total' => 0,
            'created_at' => fake()->date('Y-m-d', 'now')
        ];
    }
}
