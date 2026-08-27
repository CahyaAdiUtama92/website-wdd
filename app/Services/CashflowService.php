<?php

namespace App\Services;

use App\Models\ArusKas;
use App\Models\IuranAnggota;
use App\Models\KategoriArusKas;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CashflowService
{
    /**
     * Menghitung total seluruh pemasukan.
     */
    public static function getTotalPemasukan(): float
    {
        return (float) ArusKas::where('tipe', 'pemasukan')->sum('jumlah');
    }

    /**
     * Menghitung total seluruh pengeluaran.
     */
    public static function getTotalPengeluaran(): float
    {
        return (float) ArusKas::where('tipe', 'pengeluaran')->sum('jumlah');
    }

    /**
     * Menghitung saldo akhir (Total Pemasukan - Total Pengeluaran).
     */
    public static function getSaldo(): float
    {
        return self::getTotalPemasukan() - self::getTotalPengeluaran();
    }

    /**
     * Jumlah anggota yang sudah membayar iuran pada bulan dan tahun berjalan.
     *
     * @return array{lunas: int, total: int}
     */
    public static function getIuranBulanBerjalan(): array
    {
        $bulan = (int) now()->format('n');
        $tahun = (int) now()->format('Y');

        $lunas = IuranAnggota::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->count();

        $total = User::where('is_active', true)
            ->where('is_super_admin', false)
            ->whereNotNull('role_id')
            ->count();

        return ['lunas' => $lunas, 'total' => $total];
    }

    /**
     * Total nominal donasi pada tahun berjalan.
     */
    public static function getDonasiTahunBerjalan(): float
    {
        $tahun = now()->year;

        return (float) ArusKas::whereHas('kategori', fn ($q) => $q->where('nama', 'Donasi'))
            ->where('tipe', 'pemasukan')
            ->whereYear('tanggal_transaksi', $tahun)
            ->sum('jumlah');
    }

    /**
     * Menghitung data keuangan seorang anggota.
     *
     * @return array{total_kewajiban: float, terbayarkan: float, sisa_hutang: float}
     */
    public static function getKeuanganAnggota(User $user): array
    {
        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();

        // Hitung jumlah bulan kewajiban sejak tanggal bergabung
        $jumlahBulanKewajiban = 0;
        if ($user->tanggal_bergabung) {
            $mulai = \Carbon\Carbon::parse($user->tanggal_bergabung)->startOfMonth();
            $sekarang = now()->startOfMonth();
            $jumlahBulanKewajiban = (int) $mulai->diffInMonths($sekarang) + 1;
        }

        $totalKewajiban = $jumlahBulanKewajiban * $nominalIuran;

        // Jumlah nominal yang sudah terbayarkan (melalui relasi iuran_anggota → arus_kas)
        $terbayarkan = (float) $user->iuranAnggota()
            ->with('arusKas')
            ->get()
            ->sum(fn ($iuran) => $iuran->arusKas?->jumlah ?? 0);

        $sisaHutang = max(0, $totalKewajiban - $terbayarkan);

        return [
            'total_kewajiban'        => $totalKewajiban,
            'terbayarkan'            => $terbayarkan,
            'sisa_hutang'            => $sisaHutang,
            'jumlah_bulan_kewajiban' => $jumlahBulanKewajiban,
        ];
    }

    /**
     * Mencatat pembayaran iuran untuk rentang bulan sekaligus dalam satu transaksi DB.
     *
     * Setiap bulan menghasilkan satu baris arus_kas dan satu baris iuran_anggota.
     * Bulan yang sudah terbayar dilewati (tidak error, hanya masuk $skipped).
     *
     * @param  int  $userId      ID anggota yang membayar
     * @param  int  $bulanMulai  Bulan awal (1–12)
     * @param  int  $bulanAkhir  Bulan akhir (1–12, harus >= $bulanMulai)
     * @param  int  $tahun       Tahun pembayaran
     * @param  int  $dibuatOleh  ID user (bendahara) yang mencatat
     * @return array{created: int[], skipped: int[]}  Nomor bulan yang dibuat vs dilewati
     *
     * @throws \RuntimeException  Jika kategori "Iuran Wajib" tidak ditemukan
     */
    public static function recordBulkIuran(
        int $userId,
        int $bulanMulai,
        int $bulanAkhir,
        int $tahun,
        int $dibuatOleh
    ): array {
        // Resolve kategori & nominal di luar loop agar tidak query berulang
        $kategori = KategoriArusKas::where('nama', 'Iuran Wajib')
            ->where('tipe', 'pemasukan')
            ->first();

        if (! $kategori) {
            throw new \RuntimeException('Kategori "Iuran Wajib" tidak ditemukan. Pastikan kategori sudah tersedia.');
        }

        $nominal = Pengaturan::getNominalIuranBulanan();

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $created = [];
        $skipped = [];

        DB::transaction(function () use (
            $userId, $bulanMulai, $bulanAkhir, $tahun,
            $dibuatOleh, $kategori, $nominal, $bulanNames,
            &$created, &$skipped
        ) {
            for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
                // Cek duplikat: lewati jika sudah ada pembayaran untuk bulan ini
                $sudahAda = IuranAnggota::where('user_id', $userId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->exists();

                if ($sudahAda) {
                    $skipped[] = $bulan;
                    continue;
                }

                // Insert 1: Ledger arus kas
                $arusKas = ArusKas::create([
                    'tanggal_transaksi' => now()->toDateString(),
                    'tipe'              => 'pemasukan',
                    'kategori_id'       => $kategori->id,
                    'user_id'           => $userId,
                    'jumlah'            => $nominal,
                    'keterangan'        => 'Iuran bulan ' . $bulanNames[$bulan] . ' ' . $tahun,
                    'dibuat_oleh'       => $dibuatOleh,
                ]);

                // Insert 2: Tracker iuran_anggota (tanpa nominal, hanya referensi bulan)
                IuranAnggota::create([
                    'user_id'    => $userId,
                    'bulan'      => $bulan,
                    'tahun'      => $tahun,
                    'arus_kas_id' => $arusKas->id,
                ]);

                $created[] = $bulan;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }
}
