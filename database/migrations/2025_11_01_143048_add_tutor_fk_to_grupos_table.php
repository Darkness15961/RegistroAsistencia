<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Ahora que la tabla 'personas' existe, creamos la FK para 'id_tutor'
            $table->foreign('id_tutor')
                  ->references('id_persona')
                  ->on('personas')
                  ->onDelete('set null'); // Si se elimina el tutor, el grupo no se borra, solo queda NULL
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Nombre de la restricción por defecto en Laravel: table_column_foreign
            $table->dropForeign(['id_tutor']);
        });
    }
};