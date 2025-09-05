<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\PerformanceGuide;
use App\Models\Question;
use Illuminate\Database\Seeder;

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
    }
}
