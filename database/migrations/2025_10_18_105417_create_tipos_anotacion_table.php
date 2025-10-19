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
        Schema::create('tipos_anotacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Ej: "Felicitación", "Llamado de atención", "Falta Grave"
            $table->string('codigo', 20)->unique()->nullable();
            $table->enum('categoria', ['positiva', 'informativa', 'leve', 'grave', 'muy_grave'])->default('informativa');
            $table->enum('tipo', ['academica', 'convivencia', 'disciplinaria'])->default('convivencia');
            $table->text('descripcion')->nullable();
            $table->string('color', 20)->default('#6c757d'); // Color en hexadecimal para UI
            $table->integer('puntos')->default(0)->comment('Puntos positivos o negativos según tipo');
            $table->boolean('notificar_acudiente')->default(false);
            $table->boolean('requiere_compromiso')->default(false);
            $table->boolean('requiere_suspension')->default(false);
            $table->integer('dias_suspension')->default(0);
            $table->boolean('activa')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_anotacion');
    }
};
