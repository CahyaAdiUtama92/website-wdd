<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasi extends Model
{
    protected $fillable = ['periode_kepengurusan_id', 'jabatan_id', 'user_id'];

    public function periodeKepengurusan()
    {
        return $this->belongsTo(PeriodeKepengurusan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
