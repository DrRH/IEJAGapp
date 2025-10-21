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
        // Agregar campo numero_acta a reportes_convivencia
        Schema::table('reportes_convivencia', function (Blueprint $table) {
            $table->string('numero_acta', 50)->nullable()->after('id')->index();
        });

        // Crear tabla pivot para estudiantes involucrados
        Schema::create('estudiantes_involucrados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_id')->constrained('reportes_convivencia')->onDelete('cascade');
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->enum('rol', ['victimario', 'victima'])->default('victimario');
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['reporte_id', 'rol']);
            $table->index('estudiante_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar tabla pivot
        Schema::dropIfExists('estudiantes_involucrados');

        // Eliminar campo numero_acta
        Schema::table('reportes_convivencia', function (Blueprint $table) {
            $table->dropIndex(['numero_acta']);
            $table->dropColumn('numero_acta');
        });
    }
};
