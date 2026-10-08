<?php

namespace App\Policies;

use App\Models\Lote;
use App\Models\User;

class LotePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lote $lote): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Lote $lote): bool
    {
        return $user->rol === 'admin';
    }

    public function delete(User $user, Lote $lote): bool
    {
        return $user->rol === 'admin';
    }
}
