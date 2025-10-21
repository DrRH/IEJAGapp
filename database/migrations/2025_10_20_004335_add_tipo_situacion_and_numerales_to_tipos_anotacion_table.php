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
        Schema::table('tipos_anotacion', function (Blueprint $table) {
            // Agregar campo para tipo de situación según manual de convivencia
            $table->enum('tipo_situacion', ['tipo_i', 'tipo_ii', 'tipo_iii'])->nullable()->after('tipo');

            // Agregar campo para el numeral del manual de convivencia
            $table->string('numeral', 20)->nullable()->after('tipo_situacion')->comment('Numeral del manual de convivencia');

            // Índice para búsquedas rápidas
            $table->index('tipo_situacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipos_anotacion', function (Blueprint $table) {
            $table->dropIndex(['tipo_situacion']);
            $table->dropColumn(['tipo_situacion', 'numeral']);
        });
    }
};
