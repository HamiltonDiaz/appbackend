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
        estados::updateOrCreate(
            ['id' => 1],
            ['descripcion' => 'Activo']
        );

        estados::updateOrCreate(
            ['id' => 2],
            ['descripcion' => 'Inactivo']
        );

        estados::updateOrCreate(
            ['id' => 3],
            ['descripcion' => 'Eliminado']
        );
    }
}
