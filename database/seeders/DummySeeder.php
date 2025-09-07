<?php

namespace Database\Seeders;

use App\Enum\AssessmentResult;
use App\Enum\AssessmentStatus;
use App\Enum\UserType;
use App\Models\Assessment;
use App\Models\AssessmentSchedule;
use App\Models\Module;
use App\Models\PerformanceGuide;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DevSeeder::class,
        ]);

        // Create Modules and Performance Guides
        Module::factory()
            ->has(Question::factory()->count(5))
            ->count(25)
            ->create();

        Module::all()->each(function (Module $module) {
            PerformanceGuide::factory()->create([
                'module_id' => $module->id,
                'skill_number' => $module->code,
            ]);
        });

        // Create additional assessors
        $assessors = collect();
        $assessors->push(User::where('type', UserType::Assessor)->first()); // Existing assessor

        $additionalAssessors = User::factory()->count(2)->create([
            'type' => UserType::Assessor,
            'password' => Hash::make('password'),
            'locale' => 'en',
        ]);
        $assessors = $assessors->merge($additionalAssessors);

        // Create additional assessees  
        $assessees = collect();
        $assessees->push(User::where('type', UserType::Assessee)->first()); // Existing assessee
        
        $additionalAssessees = User::factory()->count(15)->create([
            'type' => UserType::Assessee,
            'password' => Hash::make('password'),
            'locale' => 'en',
        ]);
        $assessees = $assessees->merge($additionalAssessees);

        // Get performance guides for assessments
        $performanceGuides = PerformanceGuide::all();
        
        $primaryAssessor = $assessors->first(); // Use the existing assessor for dashboard demo

        // Create assessments with various statuses for the primary assessor
        $this->createAssessments($primaryAssessor, $assessees, $performanceGuides);

        // Create some assessments for other assessors too
        $assessors->skip(1)->each(function ($assessor) use ($assessees, $performanceGuides) {
            $this->createAssessments($assessor, $assessees->random(3), $performanceGuides, 3);
        });
    }

    private function createAssessments(User $assessor, $assessees, $performanceGuides, int $count = null): void
    {
        $count = $count ?? 12; // Default to 12 for primary assessor
        
        for ($i = 0; $i < $count; $i++) {
            $assessee = $assessees->random();
            $guide = $performanceGuides->random();
            
            // Determine assessment status with realistic distribution
            if ($i < 3) {
                $status = AssessmentStatus::Pending;
                $result = null;
                $startedAt = null;
                $finishedAt = null;
                $scheduledAt = null;
            } elseif ($i < 5) {
                $status = AssessmentStatus::Scheduled;
                $result = null;
                $startedAt = null;
                $finishedAt = null;
                $scheduledAt = now()->addDays(rand(1, 14));
            } elseif ($i < 10) {
                $status = AssessmentStatus::Completed;
                $result = fake()->randomElement([AssessmentResult::Competent, AssessmentResult::NotCompetent]);
                $startedAt = now()->subDays(rand(1, 30));
                $finishedAt = $startedAt->copy()->addHours(rand(1, 4));
                $scheduledAt = $startedAt->copy()->subDays(1);
            } else {
                $status = AssessmentStatus::Cancelled;
                $result = null;
                $startedAt = null;
                $finishedAt = null;
                $scheduledAt = now()->subDays(rand(1, 7));
            }

            $assessment = Assessment::create([
                'assessor_id' => $assessor->id,
                'assessee_id' => $assessee->id,
                'assessee_name' => $assessee->name,
                'assessee_no_id' => fake()->numerify('####-####'),
                'assessee_school' => fake()->company() . ' ' . fake()->randomElement(['School', 'College', 'Institute']),
                'performance_guide_id' => $guide->id,
                'status' => $status->value,
                'result' => $result?->value,
                'tasks' => [
                    'task_1' => fake()->sentence(),
                    'task_2' => fake()->sentence(),
                    'task_3' => fake()->sentence(),
                ],
                'comment' => $status === AssessmentStatus::Completed ? fake()->paragraph() : null,
                'scheduled_at' => $scheduledAt,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
            ]);

            // Create assessment schedules for pending assessments
            if ($status === AssessmentStatus::Pending) {
                // Create 2-4 proposed dates
                for ($j = 0; $j < rand(2, 4); $j++) {
                    AssessmentSchedule::create([
                        'assessment_id' => $assessment->id,
                        'proposed_date' => now()->addDays(rand(3, 21))->setTime(rand(8, 16), [0, 30][rand(0, 1)]),
                        'status' => null, // null means proposed
                    ]);
                }
            }
        }
    }
}
