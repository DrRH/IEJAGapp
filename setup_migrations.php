<?php
/**
 * Script temporal para completar las migraciones del sistema educativo
 * Ejecutar con: php setup_migrations.php
 */

$migrations = [
    // Asignaturas
    '2025_10_18_105416_create_asignaturas_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->unique();
            $table->enum('area', ['matematicas', 'lenguaje', 'ciencias_naturales', 'ciencias_sociales', 'ingles', 'educacion_fisica', 'artistica', 'etica', 'religion', 'tecnologia', 'filosofia', 'otras'])->default('otras');
            $table->integer('intensidad_horaria_semanal')->default(1)->comment('Horas semanales');
            $table->boolean('es_fundamental')->default(true)->comment('Si es asignatura fundamental o no');
            $table->boolean('activa')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};
PHP,

    // Estudiantes
    '2025_10_18_105414_create_estudiantes_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_estudiante', 20)->unique();
            $table->string('tipo_documento', 20)->default('TI');
            $table->string('numero_documento', 50)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('lugar_nacimiento', 100)->nullable();
            $table->enum('tipo_sangre', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->nullable();
            $table->string('eps', 100)->nullable();

            // Información de contacto
            $table->string('direccion')->nullable();
            $table->string('barrio', 100)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('email', 100)->nullable();

            // Información académica
            $table->enum('estrato', ['1', '2', '3', '4', '5', '6'])->nullable();
            $table->string('colegio_procedencia')->nullable();
            $table->date('fecha_ingreso')->nullable();

            // Información familiar
            $table->string('nombre_acudiente', 150)->nullable();
            $table->string('parentesco_acudiente', 50)->nullable();
            $table->string('celular_acudiente', 50)->nullable();
            $table->string('email_acudiente', 100)->nullable();
            $table->string('ocupacion_acudiente', 100)->nullable();

            $table->string('nombre_madre', 150)->nullable();
            $table->string('celular_madre', 50)->nullable();
            $table->string('nombre_padre', 150)->nullable();
            $table->string('celular_padre', 50)->nullable();

            // Estado y observaciones
            $table->boolean('activo')->default(true);
            $table->enum('estado', ['activo', 'retirado', 'graduado', 'trasladado'])->default('activo');
            $table->text('observaciones_medicas')->nullable();
            $table->text('observaciones')->nullable();

            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
PHP,

    // Docentes
    '2025_10_18_105415_create_docentes_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('codigo_docente', 20)->unique();
            $table->string('tipo_documento', 20)->default('CC');
            $table->string('numero_documento', 50)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();

            // Información de contacto
            $table->string('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('email', 100)->unique();

            // Información académica y laboral
            $table->string('titulo_profesional')->nullable();
            $table->string('especializacion')->nullable();
            $table->enum('tipo_vinculacion', ['planta', 'provisional', 'contratista', 'catedra'])->default('planta');
            $table->date('fecha_ingreso')->nullable();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');
            $table->string('escalafon', 50)->nullable();

            // Estado
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
PHP,

    // Personal Administrativo
    '2025_10_18_105415_create_personal_administrativo_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_administrativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('codigo_empleado', 20)->unique();
            $table->string('tipo_documento', 20)->default('CC');
            $table->string('numero_documento', 50)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();

            // Información de contacto
            $table->string('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('email', 100)->unique();

            // Información laboral
            $table->enum('cargo', ['rector', 'coordinador', 'secretaria', 'bibliotecario', 'psicologo', 'orientador', 'auxiliar', 'otro'])->default('otro');
            $table->string('cargo_descripcion')->nullable();
            $table->enum('tipo_vinculacion', ['planta', 'provisional', 'contratista'])->default('planta');
            $table->date('fecha_ingreso')->nullable();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null');

            // Estado
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_administrativo');
    }
};
PHP,

    // Matrículas
    '2025_10_18_105416_create_matriculas_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->year('anio');
            $table->string('numero_matricula', 50)->unique();
            $table->date('fecha_matricula');
            $table->enum('estado', ['activa', 'retirada', 'cancelada', 'promovida'])->default('activa');
            $table->date('fecha_retiro')->nullable();
            $table->text('motivo_retiro')->nullable();
            $table->boolean('repitente')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['estudiante_id', 'grupo_id', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
PHP,

    // Asignaciones Docente-Asignatura
    '2025_10_18_105417_create_asignaciones_docente_asignatura_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_docente_asignatura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->year('anio');
            $table->integer('intensidad_horaria')->default(1);
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['docente_id', 'asignatura_id', 'grupo_id', 'anio'], 'unique_asignacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_docente_asignatura');
    }
};
PHP,

    // Calificaciones
    '2025_10_18_105417_create_calificaciones_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->foreignId('asignatura_id')->constrained('asignaturas')->onDelete('cascade');
            $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->onDelete('cascade');
            $table->decimal('nota_cuantitativa', 4, 2)->nullable()->comment('Nota de 0.00 a 5.00');
            $table->enum('nota_cualitativa', ['superior', 'alto', 'basico', 'bajo'])->nullable();
            $table->integer('fallas')->default(0)->comment('Número de inasistencias');
            $table->text('observaciones')->nullable();
            $table->text('fortalezas')->nullable();
            $table->text('debilidades')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_registro')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['matricula_id', 'asignatura_id', 'periodo_academico_id'], 'unique_calificacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
PHP,

    // Asistencias
    '2025_10_18_105417_create_asistencias_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('asignatura_id')->nullable()->constrained('asignaturas')->onDelete('set null');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tarde', 'excusa'])->default('presente');
            $table->time('hora_llegada')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('excusa_justificada')->default(false);
            $table->text('justificacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estudiante_id', 'fecha']);
            $table->index(['grupo_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
PHP,

    // Tipos de Anotación
    '2025_10_18_105417_create_tipos_anotacion_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_anotacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->unique();
            $table->enum('categoria', ['positiva', 'negativa', 'neutra'])->default('neutra');
            $table->enum('gravedad', ['leve', 'moderada', 'grave', 'muy_grave'])->nullable();
            $table->text('descripcion')->nullable();
            $table->string('color', 20)->nullable()->comment('Color para visualización');
            $table->boolean('notificar_acudiente')->default(false);
            $table->boolean('requiere_seguimiento')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_anotacion');
    }
};
PHP,

    // Reportes de Convivencia
    '2025_10_18_105418_create_reportes_convivencia_table.php' => <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_convivencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('tipo_anotacion_id')->constrained('tipos_anotacion')->onDelete('restrict');
            $table->foreignId('reportado_por')->constrained('users')->onDelete('restrict');
            $table->date('fecha_reporte');
            $table->time('hora_reporte')->nullable();
            $table->text('descripcion');
            $table->text('acciones_tomadas')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('acudiente_notificado')->default(false);
            $table->date('fecha_notificacion_acudiente')->nullable();
            $table->enum('estado', ['registrado', 'en_seguimiento', 'resuelto', 'cerrado'])->default('registrado');
            $table->foreignId('responsable_seguimiento')->nullable()->constrained('users')->onDelete('set null');
            $table->text('seguimiento')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estudiante_id', 'fecha_reporte']);
            $table->index(['tipo_anotacion_id', 'fecha_reporte']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_convivencia');
    }
};
PHP,
];

echo "Actualizando migraciones...\n\n";

foreach ($migrations as $filename => $content) {
    $filepath = __DIR__ . '/database/migrations/' . $filename;

    if (file_exists($filepath)) {
        file_put_contents($filepath, $content);
        echo "✓ Actualizada: $filename\n";
    } else {
        echo "✗ No encontrada: $filename\n";
    }
}

echo "\n✓ Proceso completado!\n";
