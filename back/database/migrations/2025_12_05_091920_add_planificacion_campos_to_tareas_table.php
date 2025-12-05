<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            // Tiempo estimado en minutos
            $table->unsignedInteger('tiempo_estimado')->nullable()->after('estado');

            // Prioridad: Normal / Alta
            $table->string('prioridad')->default('Normal')->after('tiempo_estimado');

            // Planificación de estudio
            $table->date('fecha_planificada')->nullable()->after('prioridad');
            $table->time('hora_inicio_plan')->nullable()->after('fecha_planificada');
            $table->time('hora_fin_plan')->nullable()->after('hora_inicio_plan');

            // Descansos
            $table->unsignedInteger('descanso_duracion')->default(10)->after('hora_fin_plan'); // minutos
            $table->unsignedInteger('descanso_cada')->default(50)->after('descanso_duracion'); // cada X minutos de estudio
        });
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn([
                'tiempo_estimado',
                'prioridad',
                'fecha_planificada',
                'hora_inicio_plan',
                'hora_fin_plan',
                'descanso_duracion',
                'descanso_cada',
            ]);
        });
    }
};
