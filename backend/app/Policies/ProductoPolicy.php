<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    /**
     * Determina si el usuario puede ver cualquier producto.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver un producto específico.
     */
    public function view(User $user, Producto $producto): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede crear productos.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede actualizar un producto.
     */
    public function update(User $user, Producto $producto): bool
    {
        return $user->rol === 'admin';
    }

    /**
     * Determina si el usuario puede eliminar un producto.
     */
    public function delete(User $user, Producto $producto): bool
    {
        return $user->rol === 'admin';
    }
}
