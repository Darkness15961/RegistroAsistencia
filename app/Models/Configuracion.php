<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';
    protected $primaryKey = 'clave'; // Le decimos a Laravel que la PK es 'clave'
    public $incrementing = false; // Le decimos que no es un número autoincremental
    protected $keyType = 'string'; // Le decimos que es un string

    protected $fillable = ['clave', 'valor'];
}