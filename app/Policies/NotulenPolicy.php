<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notulen;

class NotulenPolicy
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

    public function view(User $user, Notulen $notulen): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function update(User $user, Notulen $notulen): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function delete(User $user, Notulen $notulen): bool
    {
        return $user->role?->name === 'Sekretaris';
    }
}
