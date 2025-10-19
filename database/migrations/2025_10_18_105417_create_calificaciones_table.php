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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->onDelete('cascade');
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->onDelete('set null');

            // Notas (escala 1.0 a 5.0 en Colombia)
            $table->decimal('nota_periodo', 3, 2)->nullable()->comment('Nota del período (1.0-5.0)');
            $table->decimal('nota_acumulada', 3, 2)->nullable()->comment('Nota acumulada hasta el momento');
            $table->decimal('nota_final', 3, 2)->nullable()->comment('Nota final del año');

            // Desempeño cualitativo (Decreto 1290)
            $table->enum('desempeno', ['superior', 'alto', 'basico', 'bajo'])->nullable();

            // Observaciones y conceptos
            $table->text('fortalezas')->nullable();
            $table->text('debilidades')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->text('observaciones')->nullable();

            // Inasistencias que afectan la nota
            $table->integer('faltas_periodo')->default(0);

            // Seguimiento
            $table->boolean('aprobada')->nullable()->comment('Si aprobó o no la asignatura');
            $table->boolean('requiere_nivelacion')->default(false);
            $table->date('fecha_registro')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Un estudiante tiene solo una calificación por asignatura por período
            $table->unique(['matricula_id', 'asignatura_id', 'periodo_academico_id'], 'unique_calificacion_matricula_asignatura_periodo');
            $table->index(['matricula_id', 'periodo_academico_id']);
            $table->index('desempeno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
