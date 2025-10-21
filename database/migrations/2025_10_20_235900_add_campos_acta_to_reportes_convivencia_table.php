<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar campos adicionales para el acta oficial de convivencia
     *
     * Campos agregados:
     * - contexto_situacion: Contexto detallado de la situación
     * - analisis_institucional: Análisis desde la perspectiva institucional
     * - conclusiones: Conclusiones del caso
     * - acciones_pedagogicas: Acciones pedagógicas y restaurativas aplicadas
     * - compromiso_acudiente: Compromisos específicos del acudiente
     * - compromiso_estudiante: Compromisos específicos del estudiante
     * - compromiso_institucion: Compromisos específicos de la institución
     */
    public function up(): void
    {
        Schema::table('reportes_convivencia', function (Blueprint $table) {
            // Campos de desarrollo y análisis del caso
            $table->text('contexto_situacion')->nullable()->after('evidencias');
            $table->text('analisis_institucional')->nullable()->after('contexto_situacion');
            $table->text('conclusiones')->nullable()->after('analisis_institucional');
            $table->text('acciones_pedagogicas')->nullable()->after('acciones_tomadas');

            // Campos de compromisos diferenciados
            $table->text('compromiso_acudiente')->nullable()->after('compromiso');
            $table->text('compromiso_estudiante')->nullable()->after('compromiso_acudiente');
            $table->text('compromiso_institucion')->nullable()->after('compromiso_estudiante');
        });
    }

    /**
     * Revertir los cambios
     */
    public function down(): void
    {
        Schema::table('reportes_convivencia', function (Blueprint $table) {
            $table->dropColumn([
                'contexto_situacion',
                'analisis_institucional',
                'conclusiones',
                'acciones_pedagogicas',
                'compromiso_acudiente',
                'compromiso_estudiante',
                'compromiso_institucion',
            ]);
        });
    }
};
