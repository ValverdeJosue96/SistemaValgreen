<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'carnet',
        'telefono',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}