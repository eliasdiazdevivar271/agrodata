<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lote_id'  => 'required|exists:lotes,id',
            'tipo'     => 'required|in:entrada,salida,merma',
            'cantidad' => 'required|integer|min:1',
            'motivo'   => 'required_if:tipo,merma|nullable|string|max:255',
        ];
    }
}
