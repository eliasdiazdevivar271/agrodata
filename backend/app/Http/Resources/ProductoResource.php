<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nombre'        => $this->nombre,
            'categoria'     => $this->categoria,
            'unidad_medida' => $this->unidad_medida,
            'stock_minimo'  => (int) $this->stock_minimo,
            'stock_total'   => (int) $this->stock_total,
            'activo'        => (bool) $this->activo,
            'created_at'    => $this->created_at,
        ];
    }
}
