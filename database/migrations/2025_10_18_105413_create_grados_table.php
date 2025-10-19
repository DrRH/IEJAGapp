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
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Ej: "Sexto", "Séptimo", "Octavo"
            $table->string('codigo', 20)->unique(); // Ej: "6", "7", "8", "9", "10", "11"
            $table->enum('nivel', ['preescolar', 'primaria', 'secundaria', 'media'])->default('secundaria');
            $table->integer('orden')->comment('Orden numérico para ordenar grados');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
