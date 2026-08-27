<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_kepengurusans', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_mulai', 4);
            $table->string('tahun_selesai', 4);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_kepengurusans');
    }
};
