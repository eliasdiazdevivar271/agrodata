<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClienteController extends Controller
{
    /**
     * Listado de clientes paginados.
     */
    public function index(): AnonymousResourceCollection
    {
        $clientes = Cliente::paginate(20);

        return ClienteResource::collection($clientes);
    }

    /**
     * Registrar un nuevo cliente.
     */
    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());

        return (new ClienteResource($cliente))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar un cliente específico.
     */
    public function show(Cliente $cliente): ClienteResource
    {
        return new ClienteResource($cliente);
    }

    /**
     * Actualizar los datos de un cliente.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente): ClienteResource
    {
        $cliente->update($request->validated());

        return new ClienteResource($cliente);
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado correctamente',
        ], 200);
    }
}
