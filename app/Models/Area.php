<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id_area';
    protected $fillable = ['nombre_area', 'descripcion', 'tipo_area'];

    // Un Área tiene muchos Horarios
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_area');
    }

    // Un Área tiene muchas Personas (empleados/estudiantes)
    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_area');
    }

    // Un Área tiene muchos Grupos (ej: "Alumnos Secundaria" tiene "5to A", "5to B")
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_area');
    }
}