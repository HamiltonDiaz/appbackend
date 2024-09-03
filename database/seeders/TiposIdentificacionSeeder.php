<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\tiposIdentificacion; 

class TiposIdentificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tipoId= new tiposIdentificacion();
        $tipoId->id=1;
        $tipoId->descripcion="Registro Civil";
        $tipoId->save();

        $tipoId= new tiposIdentificacion();
        $tipoId->id=2;
        $tipoId->descripcion="Tarjeta de Identidad";
        $tipoId->save();

        $tipoId= new tiposIdentificacion();
        $tipoId->id=3;
        $tipoId->descripcion="Cédula de ciudadanía";
        $tipoId->save();

        $tipoId= new tiposIdentificacion();
        $tipoId->id=4;
        $tipoId->descripcion="Cédula de extranjería";
        $tipoId->save();

        $tipoId= new tiposIdentificacion();
        $tipoId->id=5;
        $tipoId->descripcion="Pasaporte";
        $tipoId->save();
        
    }
}
