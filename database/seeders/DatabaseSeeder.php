<?php

namespace Database\Seeders;

use App\Enum\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Administrator',
            'email' => 'administrator@corsa-poliban.my.id',
            'password' => Hash::make('MtF]SLHV1b1;'),
            'type' => UserType::Admin,
            'locale' => 'en',
        ]);

        $this->call([
            RoleSeeder::class,
        ]);
    }
}
