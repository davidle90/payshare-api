<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userCount = User::all()->count();
        $ownerId = fake()->numberBetween(1, $userCount);

        return [
            'reference_id' => Str::random(5) . $ownerId,
            'name' => fake()->words(3, true),
            'owner_id' => $ownerId
        ];
    }
}
