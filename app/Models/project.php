<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'id_categoria',
        'id_estado'
    ];
    
    // Mutator para formatear 'fechainicio'
    public function setFechainicioAttribute($value)
    {
        if (!is_null($value)) {
            // Convertimos el valor en una instancia de Carbon y luego lo formateamos como 'Y-m-d'
            $this->attributes['fechainicio'] = Carbon::parse($value)->format('Y-m-d');
        } else {
            // Si el valor es null, lo asignamos directamente
            $this->attributes['fechainicio'] = null;
        }
    }

    // Mutator para formatear 'fechafin'
    public function setFechafinAttribute($value)
    {
        if (!is_null($value)) {
            // Convertimos el valor en una instancia de Carbon y luego lo formateamos como 'Y-m-d'
            $this->attributes['fechafin'] = Carbon::parse($value)->format('Y-m-d');
        } else {
            // Si el valor es null, lo asignamos directamente
            $this->attributes['fechafin'] = null;
        }
    }


    // Definir las relaciones
    public function cateogria()
    {
        return $this->belongsTo(category::class, 'id_categoria');
    }
}
