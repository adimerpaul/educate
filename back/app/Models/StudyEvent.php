<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'materia_id',
        'tipo',
        'titulo',
        'descripcion',
        'fecha',
        'hora_inicio',
        'hora_fin',
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

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}
