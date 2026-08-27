<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapats', function (Blueprint $table) {
            $table->string('judul')->after('id');
            $table->text('keterangan')->nullable()->after('agenda');
            $table->enum('status', ['dijadwalkan', 'berlangsung', 'selesai'])->default('dijadwalkan')->after('keterangan');
            $table->boolean('absensi_ditutup')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rapats', function (Blueprint $table) {
            $table->dropColumn(['judul', 'keterangan', 'status', 'absensi_ditutup']);
        });
    }
};
