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
        Schema::create('reporte_numeral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_id')->constrained('reportes_convivencia')->onDelete('cascade');
            $table->foreignId('tipo_anotacion_id')->constrained('tipos_anotacion')->onDelete('cascade');
            $table->timestamps();

            // Índices
            $table->index('reporte_id');
            $table->index('tipo_anotacion_id');

            // Evitar duplicados
            $table->unique(['reporte_id', 'tipo_anotacion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_numeral');
    }
};
