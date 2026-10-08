<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    /**
     * Lotes con cantidad_actual > 0, fecha_vencimiento no null,
     * entre hoy y hoy + $dias, ordenados por fecha de vencimiento.
     */
    public function productosPorVencer(int $dias = 30): Collection
    {
        $hoy = now()->startOfDay()->toDateString();
        $limite = now()->addDays($dias)->endOfDay()->toDateString();

        return Lote::with('producto')
            ->where('cantidad_actual', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoy, $limite])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
    }

    /**
     * Movimientos de tipo merma del mes actual agrupados por producto con su total sumado.
     */
    public function mermasDelMes(): Collection
    {
        $inicioMes = now()->startOfMonth()->toDateTimeString();
        $finMes = now()->endOfMonth()->toDateTimeString();

        return DB::table('movimientos')
            ->join('lotes', 'movimientos.lote_id', '=', 'lotes.id')
            ->join('productos', 'lotes.producto_id', '=', 'productos.id')
            ->where('movimientos.tipo', 'merma')
            ->whereBetween('movimientos.created_at', [$inicioMes, $finMes])
            ->select(
                'productos.id as producto_id',
                'productos.nombre as producto',
                'productos.unidad_medida',
                DB::raw('SUM(movimientos.cantidad) as total_merma')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.unidad_medida')
            ->get();
    }

    /**
     * Productos activos cuyo stock total sumado de sus lotes es inferior a su stock mínimo.
     */
    public function productosBajoStockMinimo(): Collection
    {
        return Producto::where('activo', true)
            ->withSum('lotes as stock_total', 'cantidad_actual')
            ->get()
            ->filter(fn (Producto $producto) => (int) ($producto->stock_total ?? 0) < (int) $producto->stock_minimo)
            ->values();
    }

    /**
     * Total monetario de ventas registradas en el mes actual.
     */
    public function ventasDelMes(): float
    {
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        return (float) Venta::whereBetween('fecha', [$inicioMes, $finMes])->sum('total');
    }
}
