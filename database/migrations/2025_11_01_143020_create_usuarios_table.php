<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->foreignId('id_persona')->constrained('personas', 'id_persona')->cascadeOnDelete();
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('rol', 20)->default('docente');
            $table->string('estado', 10)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};