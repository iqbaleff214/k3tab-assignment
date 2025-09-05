<?php

namespace Database\Seeders;

use App\Enum\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'M. Iqbal Effendi',
            'email' => 'iqbaleff214@gmail.com',
            'password' => Hash::make('password'),
            'type' => UserType::Assessor,
            'locale' => 'en',
        ]);
        User::query()->create([
            'name' => 'Mahda Nurdiana',
            'email' => 'mahdanurdiana@gmail.com',
            'password' => Hash::make('password'),
            'type' => UserType::Assessee,
            'locale' => 'en',
        ]);
    }
}
