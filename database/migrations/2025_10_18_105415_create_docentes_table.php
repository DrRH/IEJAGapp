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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios (opcional - puede haber docentes sin cuenta)
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
            $table->string('codigo_docente', 30)->unique()->nullable(); // Código interno
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');
            $table->enum('tipo_vinculacion', ['planta', 'provisional', 'contrato', 'supernumerario'])->default('planta');
            $table->string('escalafon', 50)->nullable(); // 1A, 2A, 2B, etc.
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_retiro')->nullable();
            $table->enum('nivel_estudio', ['bachiller', 'tecnico', 'tecnologo', 'profesional', 'especializacion', 'maestria', 'doctorado'])->default('profesional');
            $table->string('titulo_profesional', 200)->nullable();
            $table->string('universidad', 200)->nullable();

            // Asignación académica
            $table->json('areas_conocimiento')->nullable(); // Array de áreas que maneja
            $table->integer('horas_semanales')->default(0);
            $table->boolean('es_director_grupo')->default(false);
            $table->boolean('es_coordinador')->default(false);
            $table->boolean('es_jefe_area')->default(false);
            $table->string('area_jefatura', 100)->nullable();

            // Estado y observaciones
            $table->enum('estado', ['activo', 'inactivo', 'licencia', 'pensionado', 'retirado'])->default('activo');
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
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
        Schema::dropIfExists('docentes');
    }
};
