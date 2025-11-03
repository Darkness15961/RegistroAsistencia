<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_horario';
    protected $fillable = ['id_area', 'hora_entrada', 'hora_salida', 'turno'];

    // Un Horario pertenece a un Área
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }
}