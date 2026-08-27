<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iuran_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('bulan'); // 1-12
            $table->smallInteger('tahun'); // e.g. 2026
            $table->foreignId('arus_kas_id')->constrained('arus_kas')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            // Unique constraint: satu anggota hanya boleh bayar sekali per bulan-tahun
            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iuran_anggota');
    }
};
