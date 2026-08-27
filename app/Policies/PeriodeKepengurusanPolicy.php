<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PeriodeKepengurusan;

class PeriodeKepengurusanPolicy
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

    public function view(User $user, PeriodeKepengurusan $periodeKepengurusan): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function update(User $user, PeriodeKepengurusan $periodeKepengurusan): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function delete(User $user, PeriodeKepengurusan $periodeKepengurusan): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role?->name === 'Ketua';
    }
}
