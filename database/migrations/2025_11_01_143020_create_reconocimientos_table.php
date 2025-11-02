<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconocimientos', function (Blueprint $table) {
            $table->id('id_reconocimiento');
            $table->foreignId('id_persona')->constrained('personas', 'id_persona')->cascadeOnDelete();
            $table->text('face_descriptor'); // JSON de los rasgos faciales
            $table->string('image_url', 255)->nullable();
            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconocimientos');
    }
};