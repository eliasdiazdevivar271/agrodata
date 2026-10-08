<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVentaRequest;
use App\Http\Resources\VentaResource;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VentaController extends Controller
{
    public function __construct(
        protected VentaService $ventaService
    ) {}

    /**
     * Listado de ventas con filtros de cliente y rango de fechas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Venta::with(['cliente', 'detalles.producto']);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->query('cliente_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->query('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->query('hasta'));
        }

        $ventas = $query->latest('id')->paginate(20)->withQueryString();

        return VentaResource::collection($ventas);
    }

    /**
     * Registrar una venta con descuento automático FIFO de lotes.
     */
    public function store(StoreVentaRequest $request): JsonResponse
    {
        $venta = $this->ventaService->registrarVenta(
            $request->validated(),
            $request->user()->id
        );

        return (new VentaResource($venta))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar los detalles de una venta.
     */
    public function show(Venta $venta): VentaResource
    {
        return new VentaResource($venta->load(['cliente', 'detalles.producto']));
    }
}
