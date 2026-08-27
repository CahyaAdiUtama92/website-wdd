<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapat extends Model
{
    protected $fillable = [
        'judul',
        'tanggal',
        'waktu',
        'tempat',
        'agenda',
        'keterangan',
        'status',
        'absensi_ditutup',
        'created_by',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'absensi_ditutup'  => 'boolean',
    ];

    public function isDijadwalkan(): bool
    {
        return $this->status === 'dijadwalkan';
    }

    public function isBerlangsung(): bool
    {
        return $this->status === 'berlangsung';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function notulen()
    {
        return $this->hasOne(Notulen::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
