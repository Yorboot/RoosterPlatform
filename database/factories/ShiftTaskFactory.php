<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\ShiftTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShiftTask>
 */
class ShiftTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shift_id' => Shift::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }
}
