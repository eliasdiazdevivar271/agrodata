<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:255',
            'categoria'     => 'nullable|string|max:255',
            'unidad_medida' => 'required|string|max:20',
            'stock_minimo'  => 'nullable|integer|min:0',
            'activo'        => 'nullable|boolean',
        ];
    }
}
