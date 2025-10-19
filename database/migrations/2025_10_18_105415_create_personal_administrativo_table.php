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
        Schema::create('personal_administrativo', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios (opcional)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Información personal
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->enum('tipo_documento', ['CC', 'CE', 'PEP'])->default('CC');
            $table->string('numero_documento', 20)->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'Otro'])->default('M');

            // Información de contacto
            $table->string('direccion')->nullable();
            $table->string('municipio', 100)->default('Medellín');
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50);
            $table->string('email', 100)->unique();
            $table->string('email_institucional', 100)->nullable();

            // Información laboral
            $table->string('codigo_empleado', 30)->unique()->nullable();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');
            $table->string('cargo', 100); // Secretaria, Bibliotecario, Psicólogo, etc.
            $table->string('dependencia', 100)->nullable(); // Secretaría Académica, Rectoría, etc.
            $table->enum('tipo_vinculacion', ['planta', 'provisional', 'contrato'])->default('planta');
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_retiro')->nullable();
            $table->enum('nivel_estudio', ['bachiller', 'tecnico', 'tecnologo', 'profesional', 'especializacion', 'maestria', 'doctorado'])->default('bachiller');
            $table->string('titulo', 200)->nullable();

            // Funciones y responsabilidades
            $table->text('funciones')->nullable();
            $table->string('horario', 100)->nullable(); // Ej: "7:00 AM - 3:00 PM"
            $table->json('permisos_especiales')->nullable(); // Array de permisos del sistema

            // Estado y observaciones
            $table->enum('estado', ['activo', 'inactivo', 'licencia', 'pensionado', 'retirado'])->default('activo');
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['apellidos', 'nombres']);
            $table->index('estado');
            $table->index('cargo');
            $table->index('sede_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_administrativo');
    }
};
