<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    use HasFactory;
    // Tabla asociada
    protected $table = 'historico';

    // Las columnas que pueden ser asignadas en masa
    protected $fillable = [
        'descripcion',
        'fecha',
        'id_proyecto',
        'id_usuario',
    ];

    // Los campos que deben ser interpretados como fechas
    protected $casts = [
        'fecha' => 'datetime'
    ];

    // Definir las relaciones
    public function proyecto()
    {
        return $this->belongsTo(project::class, 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
