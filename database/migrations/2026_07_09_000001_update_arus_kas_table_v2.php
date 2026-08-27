<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arus_kas', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['kategori_arus_kas_id']);
            $table->dropForeign(['user_id']);

            // Kemudian drop kolom lama
            $table->dropColumn(['jenis', 'kategori_arus_kas_id', 'jumlah', 'keterangan', 'tanggal', 'user_id']);
        });

        Schema::table('arus_kas', function (Blueprint $table) {
            // Tambah kolom sesuai spesifikasi
            $table->date('tanggal_transaksi')->after('id');
            $table->enum('tipe', ['pemasukan', 'pengeluaran'])->after('tanggal_transaksi');
            $table->foreignId('kategori_id')->after('tipe')->constrained('kategori_arus_kas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->after('kategori_id')->constrained('users')->nullOnDelete();
            $table->decimal('jumlah', 12, 2)->after('user_id');
            $table->text('keterangan')->nullable()->after('jumlah');
            $table->foreignId('dibuat_oleh')->after('keterangan')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('arus_kas', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['dibuat_oleh']);
            $table->dropColumn(['tanggal_transaksi', 'tipe', 'kategori_id', 'user_id', 'jumlah', 'keterangan', 'dibuat_oleh']);
        });

        Schema::table('arus_kas', function (Blueprint $table) {
            $table->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $table->foreignId('kategori_arus_kas_id')->constrained('kategori_arus_kas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->bigInteger('jumlah');
            $table->string('keterangan');
            $table->date('tanggal');
        });
    }
};
