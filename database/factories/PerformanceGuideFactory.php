<?php

namespace Database\Factories;

use App\Models\PerformanceGuide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PerformanceGuide>
 */
class PerformanceGuideFactory extends Factory
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
            'performance_task' => $this->faker->paragraph(),
            'tasks' => [
                [
                    'title' => 'Prerequisite',
                    'child' => [
                        [
                            'title' => 'The student must complete the knowledge assessment. Minimum passing grade 80%.',
                            'hint' => '',
                        ]
                    ],
                ],
                ...array_map(fn ($index) => [
                    'title' => $this->faker->sentence(),
                    'child' => array_map(fn ($index) => [
                        'title' => $this->faker->sentence(),
                        'hint' => $this->faker->boolean() ? $this->faker->sentence() : '',
                    ], range(0, $this->faker->randomDigit())),
                ], range(0, $this->faker->randomDigit())),
            ],
        ];
    }
}
