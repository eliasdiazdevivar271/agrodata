<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompraRequest;
use App\Http\Resources\CompraResource;
use App\Models\Compra;
use App\Services\CompraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompraController extends Controller
{
    public function __construct(
        protected CompraService $compraService
    ) {}

    /**
     * Listado de compras con filtros de proveedor y rango de fechas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Compra::with(['proveedor', 'detalles.producto']);

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->query('proveedor_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->query('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->query('hasta'));
        }

        $compras = $query->latest('id')->paginate(20)->withQueryString();

        return CompraResource::collection($compras);
    }

    /**
     * Registrar una compra generando automáticamente los lotes de stock.
     */
    public function store(StoreCompraRequest $request): JsonResponse
    {
        $compra = $this->compraService->registrarCompra(
            $request->validated(),
            $request->user()->id
        );

        return (new CompraResource($compra))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar una compra específica con sus detalles.
     */
    public function show(Compra $compra): CompraResource
    {
        return new CompraResource($compra->load(['proveedor', 'detalles.producto']));
    }
}
