<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $table->foreignId('kategori_arus_kas_id')->constrained('kategori_arus_kas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->bigInteger('jumlah');
            $table->string('keterangan');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arus_kas');
    }
};
