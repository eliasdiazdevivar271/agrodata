<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;

class CompraService
{
    /**
     * Registra una compra y crea los lotes correspondientes con su stock inicial.
     *
     * @param  array{proveedor_id?: int|null, fecha: string, detalles: array<int, array{producto_id: int, cantidad: int, precio_unitario: float|int, fecha_vencimiento?: string|null}>}  $data
     * @param  int  $userId
     * @return Compra
     */
    public function registrarCompra(array $data, int $userId): Compra
    {
        return DB::transaction(function () use ($data, $userId) {
            $compra = Compra::create([
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'user_id'      => $userId,
                'fecha'        => $data['fecha'],
                'total'        => 0,
            ]);

            $totalCompra = 0.0;

            foreach ($data['detalles'] as $detalle) {
                $productoId = $detalle['producto_id'];
                $cantidad = (int) $detalle['cantidad'];
                $precioUnitario = (float) $detalle['precio_unitario'];
                $fechaVencimiento = $detalle['fecha_vencimiento'] ?? null;
                $subtotal = round($cantidad * $precioUnitario, 2);

                DetalleCompra::create([
                    'compra_id'         => $compra->id,
                    'producto_id'       => $productoId,
                    'cantidad'          => $cantidad,
                    'precio_unitario'   => $precioUnitario,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'subtotal'          => $subtotal,
                ]);

                // Crear Lote automáticamente
                $lote = Lote::create([
                    'producto_id'       => $productoId,
                    'cantidad_inicial'  => $cantidad,
                    'cantidad_actual'   => $cantidad,
                    'fecha_ingreso'     => $compra->fecha,
                    'fecha_vencimiento' => $fechaVencimiento,
                ]);

                // Registrar movimiento inicial de entrada
                Movimiento::create([
                    'lote_id'  => $lote->id,
                    'tipo'     => 'entrada',
                    'cantidad' => $cantidad,
                    'motivo'   => "Compra #{$compra->id}",
                ]);

                $totalCompra += $subtotal;
            }

            $compra->update(['total' => $totalCompra]);

            return $compra->load('detalles.producto', 'proveedor');
        });
    }
}
