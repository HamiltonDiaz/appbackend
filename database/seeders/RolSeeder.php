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
        $role1 = Role::create(['name' => 'superadmin', 'description'=>'Tiene acceso a todas las funcionalidades del sistema']);
        $role2 = Role::create(['name' => 'admin', 'description'=>'Encargado de crear, modifica proyectos y asignar usuarios al mismo']);
        $role3 = Role::create(['name' => 'tutor', 'description'=>'Es quien lidera el proyecto puede visualizar y crear nuevos proyectos']);
        $role4 = Role::create(['name' => 'asistente', 'description'=>'Es la persona encargada de gestionar el proyecto con la asesoría del tutor']);

        Permission::create(['name' => 'user.index', 'description'=>'Listar Usuarios'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.logout', 'description'=>'Cerrar sesion'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.me', 'description'=>'Información usuario'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'user.findById', 'description'=>'Buscar Usuario'])->syncRoles([$role1, $role2, $role3, $role4]);
        Permission::create(['name' => 'config-server.store', 'description'=>'Configuración del Servidor'])->syncRoles([$role1]);
    }
}
