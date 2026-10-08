<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovimientoRequest;
use App\Http\Resources\MovimientoResource;
use App\Models\Lote;
use App\Models\Movimiento;
use App\Services\InventarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MovimientoController extends Controller
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    /**
     * Listado de movimientos de stock con filtros de lote, producto, tipo y fechas.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Movimiento::with('lote.producto');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->query('tipo'));
        }

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->query('lote_id'));
        }

        if ($request->filled('producto_id')) {
            $query->whereHas('lote', function ($q) use ($request) {
                $q->where('producto_id', $request->query('producto_id'));
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->query('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->query('hasta'));
        }

        $movimientos = $query->latest('id')->paginate(20)->withQueryString();

        return MovimientoResource::collection($movimientos);
    }

    /**
     * Registrar un movimiento manual de stock (entrada, salida o merma).
     */
    public function store(StoreMovimientoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $lote = Lote::findOrFail($data['lote_id']);

        $movimiento = match ($data['tipo']) {
            'entrada' => $this->inventarioService->registrarEntrada($lote, $data['cantidad'], $data['motivo'] ?? null),
            'salida'  => $this->inventarioService->registrarSalida($lote, $data['cantidad'], $data['motivo'] ?? null),
            'merma'   => $this->inventarioService->registrarMerma($lote, $data['cantidad'], $data['motivo']),
        };

        return (new MovimientoResource($movimiento->load('lote.producto')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un movimiento específico.
     */
    public function show(Movimiento $movimiento): MovimientoResource
    {
        return new MovimientoResource($movimiento->load('lote.producto'));
    }
}
