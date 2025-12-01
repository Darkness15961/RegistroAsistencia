<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We use a raw statement because changing ENUM values in Doctrine/Laravel can be tricky
        // Standardizing on: admin, empleado, docente, secretaria, supervisor, estudiante
        // We also keep 'administrador' if it was there, but the goal is to support 'empleado'
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'administrador', 'empleado', 'docente', 'secretaria', 'supervisor', 'estudiante') NOT NULL DEFAULT 'empleado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to the previous state (assuming it was the one from add_secretaria_role...)
        // Note: This might fail if there are values that don't fit, but for down() it's acceptable best-effort
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'supervisor', 'docente', 'secretaria') NOT NULL");
    }
};
