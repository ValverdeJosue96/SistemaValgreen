<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'created_by',
        'updated_by',
        'estado_pedido_id',
        'fecha_pedido',
        'fecha_entrega',
        'anticipo',
        'pago_final',
        'saldo',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_pedido' => 'datetime',
        'fecha_entrega' => 'datetime',
        'anticipo' => 'decimal:2',
        'pago_final' => 'decimal:2',
        'saldo' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function estadoPedido()
    {
        return $this->belongsTo(EstadoPedido::class, 'estado_pedido_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function tortasPersonalizadas()
    {
        return $this->hasMany(DetalleTortaPersonalizada::class, 'pedido_id');
    }
}