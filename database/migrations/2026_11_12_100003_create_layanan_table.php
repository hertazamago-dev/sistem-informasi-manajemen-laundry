<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->unsignedInteger('harga');                 // rupiah per satuan
            $table->string('satuan', 10)->default('kg');      // kg | pcs
            $table->unsignedSmallInteger('estimasi_jam');
            $table->text('deskripsi')->nullable();
            // Tiga penanda ini membentuk alur status laundry per layanan
            $table->boolean('ada_cuci')->default(true);
            $table->boolean('ada_pengeringan')->default(true);
            $table->boolean('ada_setrika')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
