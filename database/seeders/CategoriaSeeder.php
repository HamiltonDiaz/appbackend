<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\category;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        category::updateOrCreate(
        ['id' => 1],
        ['descripcion' => 'Programación']
        );

        category::updateOrCreate(
            ['id' => 2],
            ['descripcion' => 'Innovación']
        );
    }
}
