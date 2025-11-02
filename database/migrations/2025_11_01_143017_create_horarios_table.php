<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id('id_horario');
            // FK de areas (se vincula el horario al área)
            $table->foreignId('id_area')->constrained('areas', 'id_area')->cascadeOnDelete();
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->string('turno', 10)->nullable(); // mañana, tarde, noche, completo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};