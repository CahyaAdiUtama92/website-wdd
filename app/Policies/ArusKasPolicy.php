<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ArusKas;

class ArusKasPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    /**
     * Seluruh anggota dapat melihat daftar arus kas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Seluruh anggota dapat melihat detail transaksi.
     */
    public function view(User $user, ArusKas $arusKas): bool
    {
        return true;
    }

    /**
     * Hanya Bendahara yang dapat membuat transaksi.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    /**
     * Hanya Bendahara yang dapat mengedit transaksi.
     */
    public function update(User $user, ArusKas $arusKas): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    /**
     * Hanya Bendahara yang dapat menghapus transaksi.
     */
    public function delete(User $user, ArusKas $arusKas): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }
}
