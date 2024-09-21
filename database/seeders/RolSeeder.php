<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'superadmin']);
        $role2 = Role::create(['name' => 'admin']);
        $role3 = Role::create(['name' => 'tutor']);
        $role4 = Role::create(['name' => 'asistente']);

        Permission::create(['name' => 'user.index'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.logout'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.me'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.findById'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'config-server.store'])->syncRoles([$role1]);
    }
}
