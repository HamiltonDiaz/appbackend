<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = "hamilton";
        $user->email = "diaz3220@hotmail.com";
        $user->password = Hash::make("admin.123");
        $user->primer_nombre = "hamilton";
        $user->primer_apellido = "diaz";
        $user->segundo_apellido = "rubio"; // nullable field
        $user->telefono = "3212479078";
        $user->numero_identificacion = "1075251751";
        $user->id_tipos_identificacion = 3;
        $user->id_estado = 1;
        $user->save();
        $user->assignRole('superadmin');
    }
}
