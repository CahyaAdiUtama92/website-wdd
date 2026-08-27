<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jabatan;

class JabatanPolicy
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

    public function view(User $user, Jabatan $jabatan): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function update(User $user, Jabatan $jabatan): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function delete(User $user, Jabatan $jabatan): bool
    {
        return $user->role?->name === 'Ketua';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role?->name === 'Ketua';
    }
}
