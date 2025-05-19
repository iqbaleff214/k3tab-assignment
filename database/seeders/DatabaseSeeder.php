<?php

namespace Database\Seeders;

use App\Enum\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin',
            'email' => 'iqbaleff214@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            RoleSeeder::class,
        ]);
    }
}
