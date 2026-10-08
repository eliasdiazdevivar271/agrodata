<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'proveedor_id' => $this->proveedor_id,
            'proveedor'    => new ProveedorResource($this->whenLoaded('proveedor')),
            'user_id'      => $this->user_id,
            'fecha'        => $this->fecha ? $this->fecha->format('Y-m-d') : null,
            'total'        => (float) $this->total,
            'detalles'     => DetalleCompraResource::collection($this->whenLoaded('detalles')),
            'created_at'   => $this->created_at,
        ];
    }
}
