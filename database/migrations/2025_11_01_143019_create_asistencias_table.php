<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id('id_asistencia');
            $table->foreignId('id_persona')->constrained('personas', 'id_persona')->cascadeOnDelete();
            $table->date('fecha')->default(DB::raw('CURRENT_DATE')); // CURRENT_DATE es SQL estándar
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->string('estado_asistencia', 10)->nullable(); // Presente, Tarde, Falta
            $table->string('metodo_registro', 10)->default('IA'); // IA, Manual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};