<?php

namespace App\Services;

use App\Models\User;

class GenerateNiaService
{
    /**
     * Menghasilkan NIA baru berdasarkan 2 digit tahun dan 4 digit urutan.
     * Contoh: 260001
     *
     * @return string
     */
    public static function generate(): string
    {
        $yearPrefix = date('y'); // Menghasilkan 2 digit akhir tahun berjalan, misal: "26"
        
        // Cari anggota terakhir yang mendaftar pada tahun ini
        $lastUser = User::where('nia', 'like', $yearPrefix . '%')
            ->orderBy('nia', 'desc')
            ->first();

        if (!$lastUser || !$lastUser->nia) {
            // Jika belum ada pada tahun ini, mulai dari 0001
            return $yearPrefix . '0001';
        }

        // Ambil 4 digit terakhir dari NIA terakhir
        $lastSequence = (int) substr($lastUser->nia, 2);
        
        // Tambahkan 1
        $nextSequence = $lastSequence + 1;

        // Pad dengan angka 0 di depan jika kurang dari 4 digit
        return $yearPrefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
