<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table = 'tipos_movimiento';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}