<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'carnet',
        'telefono',
        'usuario',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }
}
