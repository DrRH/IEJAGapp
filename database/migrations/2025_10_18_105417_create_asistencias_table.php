<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->foreignId('asignatura_id')->nullable()->constrained('asignaturas')->onDelete('set null');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tarde', 'excusa_medica', 'permiso'])->default('presente');
            $table->time('hora_llegada')->nullable();
            $table->integer('minutos_tarde')->default(0);
            $table->text('observaciones')->nullable();
            $table->string('justificacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Un estudiante tiene un solo registro de asistencia por fecha y asignatura
            $table->unique(['matricula_id', 'fecha', 'asignatura_id'], 'unique_asistencia_matricula_fecha_asignatura');
            $table->index(['matricula_id', 'fecha']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
