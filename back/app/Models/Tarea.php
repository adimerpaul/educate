<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'materia_id',
        'tipo_tarea',
        'descripcion',
        'fecha_entrega',
        'fecha_creacion',
        'estado',
        'user_id',

        // nuevos campos
        'tiempo_estimado',
        'prioridad',
        'fecha_planificada',
        'hora_inicio_plan',
        'hora_fin_plan',
        'descanso_duracion',
        'descanso_cada',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
