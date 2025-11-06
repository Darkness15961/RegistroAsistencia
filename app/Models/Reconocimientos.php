<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconocimientos extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_reconocimiento';
    protected $fillable = ['id_persona', 'face_descriptor', 'image_url', 'fecha_registro'];

    // Un Reconocimiento pertenece a una Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
