<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable; 

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_persona',
        'email',
        'password_hash',
        'rol',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Laravel usa este método internamente para verificar contraseñas.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relación con la tabla persona.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
