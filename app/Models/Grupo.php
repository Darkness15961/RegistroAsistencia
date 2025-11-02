<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_grupo';
    protected $fillable = ['id_area', 'id_tutor', 'nivel', 'grado', 'seccion'];

    // Un Grupo pertenece a un Área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    // Un Grupo tiene un Tutor (que es una Persona)
    public function tutor()
    {
        return $this->belongsTo(Persona::class, 'id_tutor', 'id_persona');
    }

    // Un Grupo tiene muchos Estudiantes (que son Personas)
    public function estudiantes()
    {
        return $this->hasMany(Persona::class, 'id_grupo');
    }
}