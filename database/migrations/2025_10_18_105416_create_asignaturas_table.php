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
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150); // Ej: "Matemáticas", "Lengua Castellana", "Ciencias Naturales"
            $table->string('codigo', 20)->unique()->nullable();
            $table->string('area', 100); // Ej: "Matemáticas", "Ciencias Naturales", "Humanidades"
            $table->text('descripcion')->nullable();
            $table->integer('intensidad_horaria')->default(0)->comment('Horas semanales');
            $table->boolean('es_fundamental')->default(true)->comment('Si es del plan de estudios básico');
            $table->boolean('es_optativa')->default(false);
            $table->boolean('aprueba_estudiante')->default(true)->comment('Si reprueba afecta promoción');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('area');
            $table->index('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};
