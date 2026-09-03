<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\User;

class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'tipo_movimiento_id',
        'created_by',
        'cantidad',
        'motivo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}