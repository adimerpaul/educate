<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Perfil del usuario (datos personales + descansos)
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('username')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['MUJER', 'VARON'])->nullable();

            $table->string('pais')->nullable();
            $table->string('estado_provincia')->nullable();
            $table->string('tipo_centro')->nullable();
            $table->string('nombre_centro')->nullable();
            $table->string('etapa')->nullable();
            $table->string('curso')->nullable();
            $table->string('clase_letra')->nullable();

            // configuración de descansos
            $table->integer('minutos_descanso')->default(10);      // ej: 10 min
            $table->integer('minutos_estudio_max')->default(50);   // ej: 50 min

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // Horarios de estudio
        Schema::create('study_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // 1 = LUN, 2 = MAR, ..., 7 = DOM
            $table->unsignedTinyInteger('dia_semana');

            // HABITUAL o EXTRA
            $table->enum('tipo', ['HABITUAL', 'EXTRA'])->default('HABITUAL');

            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // Flag para saber si ya terminó el wizard
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')
                ->default(false)
                ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed');
        });

        Schema::dropIfExists('study_schedules');
        Schema::dropIfExists('profiles');
    }
};
