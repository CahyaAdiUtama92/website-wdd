<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengaturan;
use App\Models\KategoriArusKas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Services\GenerateNiaService;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Administrator
        User::create([
            'nia'             => GenerateNiaService::generate(),
            'name'            => 'Super Administrator',
            'email'           => 'admin@wdd.local',
            'password'        => Hash::make('password'),
            'is_super_admin'  => true,
            'is_active'       => true,
        ]);

        // Pengaturan: nominal iuran bulanan
        Pengaturan::create([
            'key'   => 'nominal_iuran_bulanan',
            'value' => '10000',
        ]);

        // Kategori Arus Kas (Pemasukan)
        KategoriArusKas::create(['nama' => 'Iuran Wajib',  'tipe' => 'pemasukan']);
        KategoriArusKas::create(['nama' => 'Donasi',       'tipe' => 'pemasukan']);

        // Kategori Arus Kas (Pengeluaran)
        KategoriArusKas::create(['nama' => 'Operasional',   'tipe' => 'pengeluaran']);
        KategoriArusKas::create(['nama' => 'Peralatan',     'tipe' => 'pengeluaran']);
        KategoriArusKas::create(['nama' => 'Bantuan Sosial','tipe' => 'pengeluaran']);
        KategoriArusKas::create(['nama' => 'Konsumsi',      'tipe' => 'pengeluaran']);
        KategoriArusKas::create(['nama' => 'Lainnya',       'tipe' => 'pengeluaran']);
    }
}
