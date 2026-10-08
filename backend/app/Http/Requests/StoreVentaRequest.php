<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'                  => 'nullable|exists:clientes,id',
            'fecha'                       => 'required|date',
            'detalles'                    => 'required|array|min:1',
            'detalles.*.producto_id'      => 'required|exists:productos,id',
            'detalles.*.cantidad'         => 'required|integer|min:1',
            'detalles.*.precio_unitario'  => 'required|numeric|min:0',
        ];
    }
}
