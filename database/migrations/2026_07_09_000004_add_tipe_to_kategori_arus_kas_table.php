<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_arus_kas', function (Blueprint $table) {
            // tipe digunakan untuk filter kategori berdasarkan jenis transaksi
            $table->enum('tipe', ['pemasukan', 'pengeluaran'])->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_arus_kas', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
