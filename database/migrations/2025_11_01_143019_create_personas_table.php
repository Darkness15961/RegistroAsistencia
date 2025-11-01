<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id('id_persona');
            $table->string('nombre_completo', 150)->nullable();
            $table->string('dni', 20)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('cargo_grado', 100)->nullable();
            $table->string('tipo_persona', 10); // empleado, estudiante
            
            // FK de areas (para el horario)
            $table->foreignId('id_area')->nullable()->constrained('areas', 'id_area');
            
            // FK de grupos (solo para estudiantes)
            $table->foreignId('id_grupo')->nullable()->constrained('grupos', 'id_grupo');
            
            $table->string('estado', 10)->default('activo'); // activo, inactivo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};