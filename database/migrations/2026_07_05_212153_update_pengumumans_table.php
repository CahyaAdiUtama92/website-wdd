<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            if (Schema::hasColumn('pengumumans', 'isi')) {
                $table->renameColumn('isi', 'keterangan');
            }
            if (Schema::hasColumn('pengumumans', 'is_active')) {
                $table->renameColumn('is_active', 'is_published');
            }
        });

        Schema::table('pengumumans', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
            if (!Schema::hasColumn('pengumumans', 'file')) {
                $table->string('file')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('pengumumans', 'dibuat_oleh')) {
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete()->after('file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            $table->dropForeign(['dibuat_oleh']);
            $table->dropColumn(['file', 'dibuat_oleh']);
            $table->renameColumn('keterangan', 'isi');
            $table->renameColumn('is_published', 'is_active');
        });

        Schema::table('pengumumans', function (Blueprint $table) {
            $table->text('isi')->nullable(false)->change();
        });
    }
};
