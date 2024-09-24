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

        $user2 = new User();
        $user2->name = "pablo";
        $user2->email = "jpcortesmacias0@gmail.com";
        $user2->password = Hash::make("admin.123");
        $user2->primer_nombre = "juan";
        $user2->otros_nombres = "pablo";
        $user2->primer_apellido = "cortes";
        $user2->segundo_apellido = "macias"; // nullable field
        $user2->telefono = "3203974012";
        $user2->numero_identificacion = "1076482939";
        $user2->id_tipos_identificacion = 3;
        $user2->id_estado = 1;
        $user2->save();
        $user2->assignRole('superadmin');
    }
}
