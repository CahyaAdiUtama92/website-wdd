<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notulens', function (Blueprint $table) {
            $table->text('keputusan_rapat')->nullable()->after('isi_notulen');
            $table->text('catatan_tambahan')->nullable()->after('keputusan_rapat');
        });
    }

    public function down(): void
    {
        Schema::table('notulens', function (Blueprint $table) {
            $table->dropColumn(['keputusan_rapat', 'catatan_tambahan']);
        });
    }
};
