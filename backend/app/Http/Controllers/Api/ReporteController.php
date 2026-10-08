<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoteResource;
use App\Http\Resources\ProductoResource;
use App\Services\ReporteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        protected ReporteService $reporteService
    ) {}

    /**
     * Resumen consolidado para el dashboard.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'productos_por_vencer'  => LoteResource::collection($this->reporteService->productosPorVencer()),
            'mermas_del_mes'        => $this->reporteService->mermasDelMes(),
            'productos_bajo_stock'  => ProductoResource::collection($this->reporteService->productosBajoStockMinimo()),
            'ventas_del_mes'        => [
                'total' => $this->reporteService->ventasDelMes(),
            ],
        ]);
    }

    /**
     * Reporte de productos próximos a vencer.
     */
    public function porVencer(Request $request): JsonResponse
    {
        $dias = (int) $request->query('dias', 30);
        $lotes = $this->reporteService->productosPorVencer($dias);

        return response()->json(LoteResource::collection($lotes));
    }

    /**
     * Reporte de mermas registradas en el mes actual.
     */
    public function mermas(): JsonResponse
    {
        return response()->json($this->reporteService->mermasDelMes());
    }

    /**
     * Reporte de productos con stock por debajo del mínimo.
     */
    public function bajoStock(): JsonResponse
    {
        $productos = $this->reporteService->productosBajoStockMinimo();

        return response()->json(ProductoResource::collection($productos));
    }

    /**
     * Total de ventas en el mes actual.
     */
    public function ventasMes(): JsonResponse
    {
        return response()->json([
            'total' => $this->reporteService->ventasDelMes(),
        ]);
    }
}
