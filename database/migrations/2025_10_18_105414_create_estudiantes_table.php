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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();

            // Información personal
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->enum('tipo_documento', ['TI', 'CC', 'CE', 'RC', 'NUIP'])->default('TI');
            $table->string('numero_documento', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F', 'Otro'])->default('M');
            $table->string('grupo_sanguineo', 10)->nullable();
            $table->string('rh', 10)->nullable();
            $table->string('lugar_nacimiento', 100)->nullable();

            // Información de contacto
            $table->string('direccion')->nullable();
            $table->string('barrio', 100)->nullable();
            $table->string('municipio', 100)->default('Medellín');
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('email', 100)->nullable();

            // Información familiar
            $table->string('nombre_acudiente', 200);
            $table->string('telefono_acudiente', 50);
            $table->string('email_acudiente', 100)->nullable();
            $table->string('parentesco_acudiente', 50)->nullable(); // Madre, Padre, Abuelo, etc.

            $table->string('nombre_madre', 200)->nullable();
            $table->string('telefono_madre', 50)->nullable();
            $table->string('nombre_padre', 200)->nullable();
            $table->string('telefono_padre', 50)->nullable();

            // Información académica
            $table->string('codigo_estudiante', 30)->unique()->nullable(); // Código interno
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');
            $table->enum('estrato', ['1', '2', '3', '4', '5', '6'])->default('2');
            $table->string('eps', 100)->nullable();

            // Estado y observaciones
            $table->enum('estado', ['activo', 'inactivo', 'retirado', 'trasladado', 'graduado'])->default('activo');
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_retiro')->nullable();
            $table->text('motivo_retiro')->nullable();
            $table->text('observaciones_medicas')->nullable();
            $table->text('observaciones_generales')->nullable();

            // Necesidades educativas especiales
            $table->boolean('tiene_discapacidad')->default(false);
            $table->string('tipo_discapacidad', 200)->nullable();
            $table->text('adaptaciones_curriculares')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas frecuentes
            $table->index(['apellidos', 'nombres']);
            $table->index('estado');
            $table->index('sede_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
