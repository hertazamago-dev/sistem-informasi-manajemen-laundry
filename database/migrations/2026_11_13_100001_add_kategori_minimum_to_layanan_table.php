<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * kategori : pengelompokan di daftar harga (mis. "Kiloan - Reguler 2 Hari", "Selimut")
     * minimum  : jumlah minimum yang ditagih (mis. kiloan minimum 3 kg). 0 = tanpa minimum.
     */
    public function up(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->string('kategori', 50)->nullable()->after('nama')->index();
            $table->decimal('minimum', 8, 2)->default(0)->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropColumn(['kategori', 'minimum']);
        });
    }
};
