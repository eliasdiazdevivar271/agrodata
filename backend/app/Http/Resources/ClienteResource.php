<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nombre'     => $this->nombre,
            'ruc'        => $this->ruc,
            'telefono'   => $this->telefono,
            'direccion'  => $this->direccion,
            'created_at' => $this->created_at,
        ];
    }
}
