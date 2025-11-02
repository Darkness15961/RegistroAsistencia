<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_asistencia';
    protected $fillable = [
        'id_persona',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado_asistencia',
        'metodo_registro'
    ];

    // Una Asistencia pertenece a una Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}