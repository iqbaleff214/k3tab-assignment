<?php

namespace Database\Seeders;

use App\Enum\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::query()->createOrFirst([
            'name' => 'admin',
        ]);

        $user = User::query()->first();
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        foreach (Permission::values() as $permission) {
            $p = \App\Models\Permission::query()->firstOrCreate([
                'name' => $permission,
            ]);

            if (in_array($permission, Permission::skip()))
                continue;

            if ($admin->hasPermissionTo($p))
                continue;

            $admin->givePermissionTo($p);
        }
    }
}
