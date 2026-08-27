<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pengumuman;
use App\Models\Rapat;

class DashboardService
{
    /**
     * Mengumpulkan semua data statistik untuk ditampilkan di Dashboard Filament.
     *
     * @return array
     */
    public static function getStatistics(): array
    {
        $iuranBulanBerjalan = CashflowService::getIuranBulanBerjalan();

        return [
            'total_anggota'         => User::where('is_active', true)->where('is_super_admin', false)->count(),
            'total_pemasukan'       => CashflowService::getTotalPemasukan(),
            'total_pengeluaran'     => CashflowService::getTotalPengeluaran(),
            'saldo_kas'             => CashflowService::getSaldo(),
            'iuran_bulan_berjalan'  => $iuranBulanBerjalan,
            'donasi_tahun_berjalan' => CashflowService::getDonasiTahunBerjalan(),
            'pengumuman_terbaru'    => Pengumuman::where('is_published', true)->latest()->take(3)->get(),
            'jadwal_rapat_terdekat' => Rapat::where('tanggal', '>=', date('Y-m-d'))->orderBy('tanggal', 'asc')->first(),
        ];
    }

    /**
     * Mengumpulkan data keuangan untuk dashboard anggota.
     *
     * @param User $user
     * @return array
     */
    public static function getKeuanganAnggota(User $user): array
    {
        return CashflowService::getKeuanganAnggota($user);
    }
}
