<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre',
        'apellidos',
        'username',
        'fecha_nacimiento',
        'genero',
        'pais',
        'estado_provincia',
        'tipo_centro',
        'nombre_centro',
        'etapa',
        'curso',
        'clase_letra',
        'minutos_descanso',
        'minutos_estudio_max',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
