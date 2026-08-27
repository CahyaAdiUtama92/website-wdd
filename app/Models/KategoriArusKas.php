<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriArusKas extends Model
{
    protected $fillable = ['nama', 'tipe'];

    /**
     * Seluruh transaksi arus kas pada kategori ini.
     */
    public function arusKas()
    {
        return $this->hasMany(ArusKas::class, 'kategori_id');
    }
}
