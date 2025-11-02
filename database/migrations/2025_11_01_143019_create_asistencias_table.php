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

        // CLAVE FORÁNEA CORREGIDA: Solo una línea y usando la sintaxis moderna
        $table->foreignId('id_persona') 
              ->constrained('personas', 'id_persona') 
              ->cascadeOnDelete(); // Equivalente a ->onDelete('cascade')

        // Uso de useCurrent() para valor por defecto de la fecha
        $table->date('fecha')->useCurrent(); 
        
        $table->time('hora_entrada')->nullable();
        $table->time('hora_salida')->nullable();
        $table->string('estado_asistencia', 10)->nullable(); 
        $table->string('metodo_registro', 10)->default('IA'); 
        
        // ¡Se eliminó la línea duplicada que causaba el fallo!
        
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};