<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoteRequest;
use App\Http\Requests\UpdateLoteRequest;
use App\Http\Resources\LoteResource;
use App\Http\Resources\MovimientoResource;
use App\Models\Lote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoteController extends Controller
{
    /**
     * Listado de lotes con filtros opcionales (producto_id, con_stock, fechas de vencimiento).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Lote::with('producto');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->query('producto_id'));
        }

        if ($request->boolean('con_stock')) {
            $query->where('cantidad_actual', '>', 0);
        }

        if ($request->filled('vencimiento_desde')) {
            $query->whereDate('fecha_vencimiento', '>=', $request->query('vencimiento_desde'));
        }

        if ($request->filled('vencimiento_hasta')) {
            $query->whereDate('fecha_vencimiento', '<=', $request->query('vencimiento_hasta'));
        }

        $lotes = $query->latest('id')->paginate(20)->withQueryString();

        return LoteResource::collection($lotes);
    }

    /**
     * Almacenar un nuevo lote.
     */
    public function store(StoreLoteRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (!isset($data['cantidad_actual'])) {
            $data['cantidad_actual'] = $data['cantidad_inicial'];
        }

        $lote = Lote::create($data);

        return (new LoteResource($lote->load('producto')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un lote específico.
     */
    public function show(Lote $lote): LoteResource
    {
        return new LoteResource($lote->load('producto'));
    }

    /**
     * Actualizar los datos de un lote.
     */
    public function update(UpdateLoteRequest $request, Lote $lote): LoteResource
    {
        $lote->update($request->validated());

        return new LoteResource($lote->load('producto'));
    }

    /**
     * Eliminar un lote.
     */
    public function destroy(Lote $lote): JsonResponse
    {
        $lote->delete();

        return response()->json([
            'message' => 'Lote eliminado correctamente',
        ], 200);
    }

    /**
     * Listar movimientos de un lote específico.
     */
    public function movimientos(Lote $lote): AnonymousResourceCollection
    {
        $movimientos = $lote->movimientos()->latest('id')->paginate(20);

        return MovimientoResource::collection($movimientos);
    }
}
