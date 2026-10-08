<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id'                => 'nullable|exists:proveedores,id',
            'fecha'                       => 'required|date',
            'detalles'                    => 'required|array|min:1',
            'detalles.*.producto_id'      => 'required|exists:productos,id',
            'detalles.*.cantidad'         => 'required|integer|min:1',
            'detalles.*.precio_unitario'  => 'required|numeric|min:0',
            'detalles.*.fecha_vencimiento' => 'nullable|date',
        ];
    }
}
