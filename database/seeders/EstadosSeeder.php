<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\estados; 

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado= new estados();
        $estado->id=1;
        $estado->descripcion="Activo";
        $estado->save();

        $estado= new estados();
        $estado->id=2;
        $estado->descripcion="Inactivo";
        $estado->save();

        $estado= new estados();
        $estado->id=3;
        $estado->descripcion="Elminado";
        $estado->save();
    }
}
