<?php

namespace Database\Seeders;

use App\Enum\Feature;
use App\Enum\Role;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'iqbaleff214@gmail.com',
        ]);
    }
}
