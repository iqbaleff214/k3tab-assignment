<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
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
            'question' => $this->faker->paragraph(),
            'choices' => $this->faker->sentences(4),
            'correct_answer_index' => $this->faker->numberBetween(0, 3),
        ];
    }
}
