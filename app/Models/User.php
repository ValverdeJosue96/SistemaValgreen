<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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

    protected $casts = [
        'estado' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relación con el rol.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Pedidos creados por este usuario.
     */
    public function pedidosCreados()
    {
        return $this->hasMany(Pedido::class, 'created_by');
    }

    /**
     * Pedidos actualizados por este usuario.
     */
    public function pedidosActualizados()
    {
        return $this->hasMany(Pedido::class, 'updated_by');
    }

    /**
     * Ventas realizadas por este usuario.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    /**
     * Movimientos de stock realizados por este usuario.
     */
    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'created_by');
    }
}