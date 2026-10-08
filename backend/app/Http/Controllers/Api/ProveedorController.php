<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Resources\ProveedorResource;
use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProveedorController extends Controller
{
    /**
     * Listado de proveedores paginados.
     */
    public function index(): AnonymousResourceCollection
    {
        $proveedores = Proveedor::paginate(20);

        return ProveedorResource::collection($proveedores);
    }

    /**
     * Registrar un nuevo proveedor.
     */
    public function store(StoreProveedorRequest $request): JsonResponse
    {
        $proveedor = Proveedor::create($request->validated());

        return (new ProveedorResource($proveedor))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un proveedor específico.
     */
    public function show(Proveedor $proveedor): ProveedorResource
    {
        return new ProveedorResource($proveedor);
    }

    /**
     * Actualizar datos de un proveedor.
     */
    public function update(UpdateProveedorRequest $request, Proveedor $proveedor): ProveedorResource
    {
        $proveedor->update($request->validated());

        return new ProveedorResource($proveedor);
    }

    /**
     * Eliminar un proveedor.
     */
    public function destroy(Proveedor $proveedor): JsonResponse
    {
        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado correctamente',
        ], 200);
    }
}
