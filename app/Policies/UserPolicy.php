<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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
        return true; // Semua bisa melihat daftar anggota
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['Sekretaris', 'Admin']);
    }

    public function update(User $user, User $model): bool
    {
        return in_array($user->role?->name, ['Sekretaris', 'Admin']) || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return false; // SDD: Tidak ada hard delete untuk user, Sekretaris hanya menonaktifkan
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role?->name, ['Sekretaris', 'Admin']);
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
