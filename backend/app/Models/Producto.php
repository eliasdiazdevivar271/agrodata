<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
        'unidad_medida',
        'stock_minimo',
        'activo',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'stock_minimo' => 'integer',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }

    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function detalleCompras(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function getStockTotalAttribute(): int
    {
        if (array_key_exists('stock_total', $this->attributes)) {
            return (int) $this->attributes['stock_total'];
        }

        if ($this->relationLoaded('lotes')) {
            return (int) $this->lotes->sum('cantidad_actual');
        }

        return (int) $this->lotes()->sum('cantidad_actual');
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('categoria', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}
