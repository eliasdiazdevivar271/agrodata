<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleCompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'producto_id'       => $this->producto_id,
            'producto'          => new ProductoResource($this->whenLoaded('producto')),
            'cantidad'          => (int) $this->cantidad,
            'precio_unitario'   => (float) $this->precio_unitario,
            'fecha_vencimiento' => $this->fecha_vencimiento ? $this->fecha_vencimiento->format('Y-m-d') : null,
            'subtotal'          => (float) $this->subtotal,
        ];
    }
}
