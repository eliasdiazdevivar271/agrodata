<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'cliente_id' => $this->cliente_id,
            'cliente'    => new ClienteResource($this->whenLoaded('cliente')),
            'user_id'    => $this->user_id,
            'fecha'      => $this->fecha ? $this->fecha->format('Y-m-d') : null,
            'total'      => (float) $this->total,
            'detalles'   => DetalleVentaResource::collection($this->whenLoaded('detalles')),
            'created_at' => $this->created_at,
        ];
    }
}
