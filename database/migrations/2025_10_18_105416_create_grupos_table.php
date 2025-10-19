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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->year('anio');
            $table->string('nombre', 10); // Ej: "A", "B", "C", "1", "2"
            $table->foreignId('director_grupo_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('capacidad_maxima')->default(40);
            $table->string('salon', 50)->nullable(); // Ej: "Salón 201"
            $table->enum('jornada', ['mañana', 'tarde', 'noche', 'única'])->default('única');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['grado_id', 'sede_id', 'anio', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
