<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 25)->unique();             // LDR-YYYYMMDD-0001
            $table->dateTime('tanggal_masuk')->index();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // petugas penerima
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('total_dibayar')->default(0); // cache, dijaga PembayaranService
            $table->dateTime('estimasi_selesai');
            $table->string('status_laundry', 20)->default('menunggu')->index();
            $table->string('status_pembayaran', 20)->default('belum_dibayar')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
