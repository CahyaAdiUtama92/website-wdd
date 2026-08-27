<?php

namespace App\Policies;

use App\Models\User;
use App\Models\IuranAnggota;

class IuranAnggotaPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    /**
     * Bendahara dapat melihat daftar seluruh iuran anggota.
     * Anggota dapat melihat riwayat iuran mereka sendiri.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['Bendahara', 'Ketua']);
    }

    /**
     * Bendahara dapat melihat detail iuran.
     * Anggota hanya dapat melihat iuran milik diri sendiri.
     */
    public function view(User $user, IuranAnggota $iuranAnggota): bool
    {
        if ($user->role?->name === 'Anggota') {
            return $iuranAnggota->user_id === $user->id;
        }

        return in_array($user->role?->name, ['Bendahara', 'Ketua']);
    }

    /**
     * Hanya Bendahara yang dapat membuat catatan iuran.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    /**
     * Tidak boleh update iuran (immutable).
     */
    public function update(User $user, IuranAnggota $iuranAnggota): bool
    {
        return false;
    }

    /**
     * Hanya Bendahara yang dapat menghapus catatan iuran.
     */
    public function delete(User $user, IuranAnggota $iuranAnggota): bool
    {
        return $user->role?->name === 'Bendahara';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role?->name === 'Bendahara';
    }
}
