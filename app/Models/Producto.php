<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Categoria;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'estado_producto_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
