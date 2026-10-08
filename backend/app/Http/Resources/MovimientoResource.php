<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'lote_id'    => $this->lote_id,
            'lote'       => new LoteResource($this->whenLoaded('lote')),
            'tipo'       => $this->tipo,
            'cantidad'   => (int) $this->cantidad,
            'motivo'     => $this->motivo,
            'created_at' => $this->created_at,
        ];
    }
}
