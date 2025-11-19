<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id('id_grupo');
            $table->foreignId('id_area')->constrained('areas', 'id_area');
            $table->unsignedBigInteger('id_tutor')->nullable(); // La FK se añade al final
            $table->string('nivel', 50)->nullable(); // Inicial, Primaria, Secundaria
            $table->string('grado', 50)->nullable();
            $table->string('seccion', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};