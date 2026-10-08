<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    /**
     * Incrementa la cantidad actual del lote y crea un movimiento de tipo entrada.
     */
    public function registrarEntrada(Lote $lote, int $cantidad, ?string $motivo = null): Movimiento
    {
        return DB::transaction(function () use ($lote, $cantidad, $motivo) {
            $lote->cantidad_actual += $cantidad;
            $lote->save();

            return Movimiento::create([
                'lote_id'  => $lote->id,
                'tipo'     => 'entrada',
                'cantidad' => $cantidad,
                'motivo'   => $motivo,
            ]);
        });
    }

    /**
     * Valida stock suficiente, decrementa la cantidad actual y crea un movimiento de tipo salida.
     */
    public function registrarSalida(Lote $lote, int $cantidad, ?string $motivo = null): Movimiento
    {
        return DB::transaction(function () use ($lote, $cantidad, $motivo) {
            if ($lote->cantidad_actual < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente en el lote #{$lote->id}. Disponible: {$lote->cantidad_actual}, solicitado: {$cantidad}.",
                ]);
            }

            $lote->cantidad_actual -= $cantidad;
            $lote->save();

            return Movimiento::create([
                'lote_id'  => $lote->id,
                'tipo'     => 'salida',
                'cantidad' => $cantidad,
                'motivo'   => $motivo,
            ]);
        });
    }

    /**
     * Valida stock suficiente, decrementa la cantidad actual y crea un movimiento de tipo merma.
     */
    public function registrarMerma(Lote $lote, int $cantidad, string $motivo): Movimiento
    {
        return DB::transaction(function () use ($lote, $cantidad, $motivo) {
            if ($lote->cantidad_actual < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente en el lote #{$lote->id} para registrar la merma. Disponible: {$lote->cantidad_actual}, solicitado: {$cantidad}.",
                ]);
            }

            $lote->cantidad_actual -= $cantidad;
            $lote->save();

            return Movimiento::create([
                'lote_id'  => $lote->id,
                'tipo'     => 'merma',
                'cantidad' => $cantidad,
                'motivo'   => $motivo,
            ]);
        });
    }

    /**
     * Retorna la suma de cantidad_actual de todos los lotes de un producto.
     */
    public function stockTotalPorProducto(Producto $producto): int
    {
        return (int) $producto->lotes()->sum('cantidad_actual');
    }
}
