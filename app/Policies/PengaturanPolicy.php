<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pengaturan;

class PengaturanPolicy
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
        return false;
    }

    public function view(User $user, Pengaturan $pengaturan): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Pengaturan $pengaturan): bool
    {
        return false;
    }

    public function delete(User $user, Pengaturan $pengaturan): bool
    {
        return false;
    }
}
