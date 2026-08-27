<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArusKas extends Model
{
    protected $fillable = [
        'tanggal_transaksi',
        'tipe',
        'kategori_id',
        'user_id',
        'jumlah',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah'            => 'decimal:2',
    ];

    /**
     * Kategori transaksi (pemasukan/pengeluaran).
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriArusKas::class, 'kategori_id');
    }

    /**
     * Anggota yang membayar iuran (nullable, hanya untuk tipe iuran).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Bendahara yang mencatat transaksi ini.
     */
    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Catatan iuran anggota yang terhubung ke transaksi ini.
     */
    public function iuranAnggota()
    {
        return $this->hasOne(IuranAnggota::class);
    }
}
