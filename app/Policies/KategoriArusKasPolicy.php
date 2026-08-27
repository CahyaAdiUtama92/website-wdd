<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KategoriArusKas;

class KategoriArusKasPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    /**
     * Hanya Bendahara yang dapat melihat daftar kategori.
     */
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function view(User $user, KategoriArusKas $kategoriArusKas): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function update(User $user, KategoriArusKas $kategoriArusKas): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function delete(User $user, KategoriArusKas $kategoriArusKas): bool
    {
        return $user->role?->name === 'Bendahara';
    }
}
