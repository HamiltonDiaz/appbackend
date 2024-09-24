<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'primer_nombre',
        'otros_nombres',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'nombre_usuario',
        'telefono',
        'numero_identificacion',
        'name',
        'email_verified_at',
        'password',
        'id_tipos_identificacion',
        'id_estado',
    ];

    public static function documentExists($id_tipo_identificacion, $numero_identificacion, $userId = null)
    {
        $query = self::where('id_tipos_identificacion', $id_tipo_identificacion)
            ->where('numero_identificacion', $numero_identificacion);
        
        if ($userId) {
            $query->where('id', '!=', $userId); // Excluir el usuario actual
        }

        return $query->exists();
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
