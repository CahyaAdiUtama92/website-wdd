<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active; // Hanya anggota aktif yang bisa masuk ke dasbor
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nia',
        'name',
        'email',
        'password',
        'role_id',
        'is_super_admin',
        'is_active',
        'no_telp',
        'alamat',
        'tanggal_bergabung',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',
        'tanggal_bergabung'  => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Seluruh transaksi arus kas milik anggota ini (untuk iuran).
     */
    public function arusKas()
    {
        return $this->hasMany(ArusKas::class, 'user_id');
    }

    /**
     * Seluruh transaksi arus kas yang dibuat oleh user ini (sebagai bendahara).
     */
    public function arusKasDibuat()
    {
        return $this->hasMany(ArusKas::class, 'dibuat_oleh');
    }

    /**
     * Seluruh catatan iuran anggota (riwayat pembayaran).
     */
    public function iuranAnggota()
    {
        return $this->hasMany(IuranAnggota::class);
    }

    public function strukturOrganisasi()
    {
        return $this->hasMany(StrukturOrganisasi::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
