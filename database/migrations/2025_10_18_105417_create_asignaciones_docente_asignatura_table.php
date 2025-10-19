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
        Schema::create('asignaciones_docente_asignatura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->onDelete('cascade');
            $table->year('anio');
            $table->integer('horas_semanales')->default(0);
            $table->text('observaciones')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Un docente no puede estar asignado dos veces a la misma asignatura en el mismo grupo y período
            $table->unique(['docente_id', 'asignatura_id', 'grupo_id', 'periodo_academico_id'], 'unique_asignacion_doc_asig_grupo_periodo');
            $table->index(['docente_id', 'periodo_academico_id'], 'idx_asignacion_docente_periodo');
            $table->index(['grupo_id', 'anio'], 'idx_asignacion_grupo_anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_docente_asignatura');
    }
};
