<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Append-only: riwayat perubahan status tidak dapat diubah atau dihapus. */
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 20);
            $table->text('catatan')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->index(['transaksi_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
