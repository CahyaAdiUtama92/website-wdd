<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pengumuman;

class PengumumanPolicy
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

    public function view(User $user, Pengumuman $pengumuman): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['Ketua', 'Sekretaris']);
    }

    public function update(User $user, Pengumuman $pengumuman): bool
    {
        return in_array($user->role?->name, ['Ketua', 'Sekretaris']);
    }

    public function delete(User $user, Pengumuman $pengumuman): bool
    {
        return in_array($user->role?->name, ['Ketua', 'Sekretaris']);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role?->name, ['Ketua', 'Sekretaris']);
    }

    public function restore(User $user, Pengumuman $pengumuman): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Pengumuman $pengumuman): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
