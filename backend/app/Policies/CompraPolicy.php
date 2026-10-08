<?php

namespace App\Policies;

use App\Models\Compra;
use App\Models\User;

class CompraPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->rol === 'admin';
    }

    public function view(User $user, Compra $compra): bool
    {
        return $user->rol === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->rol === 'admin';
    }

    public function update(User $user, Compra $compra): bool
    {
        return $user->rol === 'admin';
    }

    public function delete(User $user, Compra $compra): bool
    {
        return $user->rol === 'admin';
    }
}
