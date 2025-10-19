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
        Schema::create('reportes_convivencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('tipo_anotacion_id')->constrained('tipos_anotacion')->onDelete('restrict');
            $table->foreignId('reportado_por')->constrained('users')->onDelete('restrict'); // Docente o coordinador que reporta
            $table->date('fecha_reporte');
            $table->time('hora_reporte')->nullable();
            $table->text('descripcion_hechos'); // Descripción detallada de lo sucedido
            $table->string('lugar', 200)->nullable(); // Salón, patio, etc.
            $table->text('testigos')->nullable(); // Nombres de testigos
            $table->text('evidencias')->nullable(); // Descripción de evidencias

            // Seguimiento
            $table->text('acciones_tomadas')->nullable(); // Qué se hizo al respecto
            $table->boolean('acudiente_notificado')->default(false);
            $table->date('fecha_notificacion_acudiente')->nullable();
            $table->string('medio_notificacion', 100)->nullable(); // Email, llamada, presencial
            $table->text('respuesta_acudiente')->nullable();

            // Compromisos y sanciones
            $table->boolean('requirio_compromiso')->default(false);
            $table->text('compromiso')->nullable();
            $table->date('fecha_compromiso')->nullable();
            $table->boolean('compromiso_cumplido')->nullable();

            $table->boolean('requirio_suspension')->default(false);
            $table->integer('dias_suspension')->default(0);
            $table->date('fecha_inicio_suspension')->nullable();
            $table->date('fecha_fin_suspension')->nullable();

            // Seguimiento psicosocial
            $table->boolean('remitido_psicologia')->default(false);
            $table->date('fecha_remision_psicologia')->nullable();
            $table->text('observaciones_psicologia')->nullable();

            // Cierre y estado
            $table->enum('estado', ['abierto', 'en_seguimiento', 'cerrado'])->default('abierto');
            $table->date('fecha_cierre')->nullable();
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones_cierre')->nullable();

            $table->text('observaciones_generales')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['estudiante_id', 'fecha_reporte']);
            $table->index('estado');
            $table->index('tipo_anotacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_convivencia');
    }
};
