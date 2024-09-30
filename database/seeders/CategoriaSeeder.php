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
        $categoria1= new category();
        $categoria1->id=1;
        $categoria1->descripcion="Programación";
        $categoria1->save();
        
        $categoria2= new category();
        $categoria2->id=2;
        $categoria2->descripcion="Innovación";
        $categoria2->save();
    }
}
