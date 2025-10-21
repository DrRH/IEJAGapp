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
        Schema::create('comites_convivencia', function (Blueprint $table) {
            $table->id();

            // Información del acta
            $table->string('numero_acta', 20)->unique(); // Consecutivo: CEC-2025-001
            $table->date('fecha_reunion');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('lugar', 200);

            // Resumen ejecutivo (para la tabla)
            $table->string('resumen_ejecutivo', 500);

            // Asistentes
            $table->text('asistentes'); // JSON o texto con lista de asistentes
            $table->text('invitados')->nullable(); // Invitados especiales

            // Orden del día
            $table->text('orden_dia'); // Puntos a tratar

            // Desarrollo
            $table->longText('desarrollo'); // Contenido completo del acta

            // Casos revisados (relación con reportes de convivencia)
            $table->json('casos_revisados')->nullable(); // Array de IDs de casos

            // Decisiones y compromisos
            $table->text('decisiones')->nullable();
            $table->text('compromisos')->nullable();
            $table->text('seguimiento_compromisos_anteriores')->nullable();

            // Próxima reunión
            $table->date('proxima_reunion')->nullable();
            $table->text('temas_proxima_reunion')->nullable();

            // Cierre
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['borrador', 'aprobada', 'publicada'])->default('borrador');

            // Archivos adjuntos
            $table->json('archivos_adjuntos')->nullable(); // URLs de archivos

            // Auditoría
            $table->foreignId('creado_por')->constrained('users')->onDelete('restrict');
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('fecha_reunion');
            $table->index('estado');
            $table->index('numero_acta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comites_convivencia');
    }
};
