<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tiposIdentificacion extends Model
{
    use HasFactory;
    protected $table = 'tipos_identificacion';

    protected $fillable = [
        'descripcion',
    ];
}
