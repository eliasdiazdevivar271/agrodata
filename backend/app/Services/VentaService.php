<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    /**
     * Registra una venta descontando stock mediante FIFO por fecha de vencimiento.
     *
     * @param  array{cliente_id?: int|null, fecha: string, detalles: array<int, array{producto_id: int, cantidad: int, precio_unitario: float|int}>}  $data
     * @param  int  $userId
     * @return Venta
     */
    public function registrarVenta(array $data, int $userId): Venta
    {
        return DB::transaction(function () use ($data, $userId) {
            $venta = Venta::create([
                'cliente_id' => $data['cliente_id'] ?? null,
                'user_id'    => $userId,
                'fecha'      => $data['fecha'],
                'total'      => 0,
            ]);

            $totalVenta = 0.0;

            foreach ($data['detalles'] as $detalle) {
                $productoId = $detalle['producto_id'];
                $cantidadRequerida = (int) $detalle['cantidad'];
                $precioUnitario = (float) $detalle['precio_unitario'];

                // Obtener lotes con stock disponible ordenados por FIFO (fecha de vencimiento ASC, nulls al final)
                $lotes = Lote::where('producto_id', $productoId)
                    ->where('cantidad_actual', '>', 0)
                    ->orderByRaw('fecha_vencimiento IS NULL ASC, fecha_vencimiento ASC, id ASC')
                    ->lockForUpdate()
                    ->get();

                $stockDisponible = $lotes->sum('cantidad_actual');
                if ($stockDisponible < $cantidadRequerida) {
                    $producto = Producto::find($productoId);
                    $nombre = $producto ? $producto->nombre : "ID #{$productoId}";
                    throw ValidationException::withMessages([
                        'detalles' => "Stock insuficiente para el producto '{$nombre}'. Solicitado: {$cantidadRequerida}, disponible: {$stockDisponible}.",
                    ]);
                }

                $pendiente = $cantidadRequerida;
                foreach ($lotes as $lote) {
                    if ($pendiente <= 0) {
                        break;
                    }

                    $descontar = min($lote->cantidad_actual, $pendiente);
                    $this->inventarioService->registrarSalida($lote, $descontar, "Venta #{$venta->id}");
                    $pendiente -= $descontar;
                }

                $subtotal = round($cantidadRequerida * $precioUnitario, 2);
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidadRequerida,
                    'precio_unitario' => $precioUnitario,
                    'subtotal'        => $subtotal,
                ]);

                $totalVenta += $subtotal;
            }

            $venta->update(['total' => $totalVenta]);

            return $venta->load('detalles.producto', 'cliente');
        });
    }
}
