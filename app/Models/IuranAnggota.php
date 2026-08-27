<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IuranAnggota extends Model
{
    // Nama tabel eksplisit (mencegah pluralisasi salah menjadi 'iuran_anggotas')
    protected $table = 'iuran_anggota';

    // Hanya memiliki created_at, tidak ada updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'arus_kas_id',
    ];

    protected $casts = [
        'bulan'      => 'integer',
        'tahun'      => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Anggota yang membayar iuran.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaksi arus kas yang mencatat nominal pembayaran iuran ini.
     */
    public function arusKas()
    {
        return $this->belongsTo(ArusKas::class);
    }

    /**
     * Nama bulan dalam Bahasa Indonesia.
     */
    public function getNamaBulanAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $bulanNames[$this->bulan] ?? '-';
    }
}
