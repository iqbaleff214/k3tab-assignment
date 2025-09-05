<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'body' => $this->faker->paragraph(10),
            'duration_estimation' => $this->faker->randomDigit(),
            'minimum_score' => $this->faker->numberBetween(50, 90),
            'questions_count' => $this->faker->randomDigit(),
            'code' => $this->faker->lexify('???-??????'),
            'equipment_required' => $this->faker->paragraph(),
            'procedure' => $this->faker->paragraph(),
            'reference' => $this->faker->paragraph(),
            'performance' => $this->faker->paragraph(),
        ];
    }
}
