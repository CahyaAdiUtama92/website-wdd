<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Absensi;

class AbsensiPolicy
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

    public function view(User $user, Absensi $absensi): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return $user->role?->name === 'Sekretaris';
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return $user->role?->name === 'Sekretaris';
    }
}
