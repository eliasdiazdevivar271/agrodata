<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id'       => 'sometimes|required|exists:productos,id',
            'cantidad_inicial'  => 'sometimes|required|integer|min:0',
            'fecha_ingreso'     => 'sometimes|required|date',
            'fecha_vencimiento' => 'nullable|date',
        ];
    }
}
