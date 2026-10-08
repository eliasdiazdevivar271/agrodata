<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\MovimientoResource;
use App\Http\Resources\ProductoResource;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ProductoController extends Controller
{
    /**
     * Listado de productos con filtros, búsqueda y paginación optimizada (withSum).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Producto::query()
            ->withSum('lotes as stock_total', 'cantidad_actual');

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->query('categoria'));
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        if ($request->boolean('bajo_stock')) {
            $query->havingRaw('COALESCE(stock_total, 0) < stock_minimo');
        }

        $productos = $query->paginate(20)->withQueryString();

        return ProductoResource::collection($productos);
    }

    /**
     * Crear un nuevo producto.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        Gate::authorize('create', Producto::class);

        $producto = Producto::create($request->validated());

        return (new ProductoResource($producto))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un producto específico con su stock total calculado.
     */
    public function show(Producto $producto): ProductoResource
    {
        Gate::authorize('view', $producto);

        $producto->loadSum('lotes as stock_total', 'cantidad_actual');

        return new ProductoResource($producto);
    }

    /**
     * Actualizar un producto existente.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): ProductoResource
    {
        Gate::authorize('update', $producto);

        $producto->update($request->validated());
        $producto->loadSum('lotes as stock_total', 'cantidad_actual');

        return new ProductoResource($producto);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        Gate::authorize('delete', $producto);

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente',
        ], 200);
    }

    /**
     * Kardex de movimientos y trazabilidad histórica del producto.
     */
    public function kardex(Producto $producto): JsonResponse
    {
        $movimientos = Movimiento::whereHas('lote', function ($query) use ($producto) {
            $query->where('producto_id', $producto->id);
        })
        ->with('lote')
        ->orderBy('created_at', 'asc')
        ->orderBy('id', 'asc')
        ->get();

        $producto->loadSum('lotes as stock_total', 'cantidad_actual');

        return response()->json([
            'producto'     => new ProductoResource($producto),
            'stock_actual' => (int) $producto->stock_total,
            'movimientos'  => MovimientoResource::collection($movimientos),
        ]);
    }
}
