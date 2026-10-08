<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'sometimes|required|string|max:255',
            'categoria'     => 'nullable|string|max:255',
            'unidad_medida' => 'sometimes|required|string|max:20',
            'stock_minimo'  => 'nullable|integer|min:0',
            'activo'        => 'nullable|boolean',
        ];
    }
}
