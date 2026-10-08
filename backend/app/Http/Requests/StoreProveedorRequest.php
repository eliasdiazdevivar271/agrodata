<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => 'required|string|max:255',
            'ruc'       => 'nullable|string|max:20',
            'telefono'  => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:500',
        ];
    }
}
