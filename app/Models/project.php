<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project extends Model
{
    use HasFactory;
    protected $table = 'proyectos';
    protected $fillable = [
        'titulo',
        'palabras_claves',
        'descripcion',
        'fechainicio',
        'fechafin',
        'ruta',
        'id_categoria'
    ];
}
