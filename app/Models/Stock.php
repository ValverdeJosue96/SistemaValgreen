<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Stock extends Model
{
    protected $table = 'stock';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}