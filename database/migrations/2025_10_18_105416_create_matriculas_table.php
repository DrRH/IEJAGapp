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
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->onDelete('cascade');
            $table->year('anio');
            $table->string('numero_matricula', 50)->unique()->nullable(); // Folio o número oficial
            $table->date('fecha_matricula');
            $table->enum('tipo_matricula', ['nueva', 'renovacion', 'traslado', 'inclusion'])->default('renovacion');
            $table->enum('jornada', ['mañana', 'tarde', 'noche', 'única', 'fin_de_semana'])->default('única');
            $table->enum('estado', ['activa', 'cancelada', 'retirada', 'trasladada', 'culminada'])->default('activa');
            $table->date('fecha_retiro')->nullable();
            $table->string('motivo_retiro')->nullable();
            $table->boolean('repite_anio')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Un estudiante solo puede tener una matrícula activa por año
            $table->unique(['estudiante_id', 'anio', 'periodo_academico_id'], 'unique_matricula_estudiante_periodo');
            $table->index(['estudiante_id', 'estado']);
            $table->index(['grupo_id', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
