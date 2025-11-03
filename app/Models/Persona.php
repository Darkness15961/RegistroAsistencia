<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_persona';
    public $incrementing = true; // 👈 Esto indica que es autoincremental
    protected $keyType = 'int';  // 👈 Esto indica que es un número (no string)

    protected $fillable = [
        'nombre_completo',
        'dni',
        'telefono',
        'correo',
        'cargo_grado',
        'tipo_persona',
        'id_area',
        'id_grupo',
        'estado'
    ];

    // Una Persona pertenece a un Área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    // Una Persona (si es estudiante) pertenece a un Grupo
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    // Una Persona (si es tutor) puede tener muchos Grupos
    public function gruposTutorados()
    {
        return $this->hasMany(Grupo::class, 'id_tutor');
    }

    // Una Persona tiene un Usuario
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona');
    }

    // Una Persona tiene muchos registros de Asistencia
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_persona');
    }

    // Una Persona tiene muchos registros de Reconocimiento Facial
    public function reconocimientos()
    {
        return $this->hasMany(Reconocimiento::class, 'id_persona');
    }
}
