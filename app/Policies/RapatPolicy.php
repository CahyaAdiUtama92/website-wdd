<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rapat;

class RapatPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Rapat $rapat): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function update(User $user, Rapat $rapat): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function delete(User $user, Rapat $rapat): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function restore(User $user, Rapat $rapat): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Rapat $rapat): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
