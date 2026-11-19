<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Log penyiapan pesan WhatsApp (append-only). Bukan bukti pesan sampai. */
    public function up(): void
    {
        Schema::create('notifikasi_wa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->restrictOnDelete();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 20);
            $table->string('nomor_tujuan', 20);
            $table->text('pesan');
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->index(['transaksi_id', 'dibuat_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_wa');
    }
};
