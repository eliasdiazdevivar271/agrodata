<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'producto_id'       => $this->producto_id,
            'producto'          => new ProductoResource($this->whenLoaded('producto')),
            'cantidad_inicial'  => (int) $this->cantidad_inicial,
            'cantidad_actual'   => (int) $this->cantidad_actual,
            'fecha_ingreso'     => $this->fecha_ingreso ? $this->fecha_ingreso->format('Y-m-d') : null,
            'fecha_vencimiento' => $this->fecha_vencimiento ? $this->fecha_vencimiento->format('Y-m-d') : null,
            'created_at'        => $this->created_at,
        ];
    }
}
