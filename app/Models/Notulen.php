<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    protected $fillable = [
        'rapat_id',
        'isi_notulen',
        'keputusan_rapat',
        'catatan_tambahan',
        'file_dokumen',
    ];

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }
}
