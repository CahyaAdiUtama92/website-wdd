<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nominal iuran bulanan dari tabel pengaturans.
     * Default: 10000 apabila belum dikonfigurasi.
     */
    public static function getNominalIuranBulanan(): float
    {
        $pengaturan = static::where('key', 'nominal_iuran_bulanan')->first();

        return $pengaturan ? (float) $pengaturan->value : 10000;
    }

    /**
     * Ambil nilai pengaturan berdasarkan key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $pengaturan = static::where('key', $key)->first();

        return $pengaturan ? $pengaturan->value : $default;
    }
}
