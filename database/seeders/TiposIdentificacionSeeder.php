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
        tiposIdentificacion::updateOrCreate(
            ['id' => 1],
            ['descripcion' => 'Registro Civil']
        );

        tiposIdentificacion::updateOrCreate(
            ['id' => 2],
            ['descripcion' => 'Tarjeta de Identidad']
        );

        tiposIdentificacion::updateOrCreate(
            ['id' => 3],
            ['descripcion' => 'Cédula de ciudadanía']
        );

        tiposIdentificacion::updateOrCreate(
            ['id' => 4],
            ['descripcion' => 'Cédula de extranjería']
        );

        tiposIdentificacion::updateOrCreate(
            ['id' => 5],
            ['descripcion' => 'Pasaporte']
        );
    }
}
