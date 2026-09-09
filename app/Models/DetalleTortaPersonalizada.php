<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTortaPersonalizada extends Model
{
    protected $table = 'detalle_torta_personalizada';

    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'porciones',
        'sabor',
        'relleno',
        'cobertura',
        'decoracion',
        'mensaje',
        'observaciones',
        'precio',
    ];

    protected $casts = [
        'porciones' => 'integer',
        'precio' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}