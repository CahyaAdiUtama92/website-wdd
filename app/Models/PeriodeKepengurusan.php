<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeKepengurusan extends Model
{
    protected $fillable = ['tahun_mulai', 'tahun_selesai', 'is_active'];

    public function strukturOrganisasis()
    {
        return $this->hasMany(StrukturOrganisasi::class);
    }
}
