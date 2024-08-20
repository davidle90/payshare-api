<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $group_id = fake()->numberBetween(1, $groupCount);

        return [
            'reference_id' => Str::random(9) . $group_id,
            'label' => fake()->words(2, true),
            'group_id' => $group_id,
            'total' => 0,
            'created_at' => fake()->date('Y-m-d', 'now')
        ];
    }
}
