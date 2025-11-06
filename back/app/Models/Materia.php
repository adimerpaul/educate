<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    //$table->string('nombre')->nullable();
    //            $table->string('docente')->nullable();
    //            $table->text('descripcion')->nullable();
    //            $table->unsignedBigInteger('user_id')->nullable();
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'docente',
        'descripcion',
        'user_id',
    ];
    protected $hidden=[
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
