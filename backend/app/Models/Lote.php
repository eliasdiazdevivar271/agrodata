<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    protected $fillable = [
        'producto_id',
        'cantidad_inicial',
        'cantidad_actual',
        'fecha_ingreso',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'fecha_ingreso'    => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad_inicial'  => 'integer',
        'cantidad_actual'   => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}
