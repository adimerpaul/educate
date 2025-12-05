<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudySchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'dia_semana',
        'tipo',
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
}
