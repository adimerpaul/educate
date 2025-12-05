<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('materia_id')->nullable();

            // Tiempo Reservado / Tiempo de Estudio adicional
            $table->string('tipo')->default('RESERVADO'); // RESERVADO | ESTUDIO_EXTRA

            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();

            $table->date('fecha')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('materia_id')->references('id')->on('materias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_events');
    }
};
