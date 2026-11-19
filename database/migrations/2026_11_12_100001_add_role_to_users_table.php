<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Menambah kolom peran, status aktif, dan telepon pada tabel users bawaan Laravel. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telepon', 20)->nullable()->after('email');
            $table->string('role', 20)->default('petugas')->after('password')->index(); // admin | petugas
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['no_telepon', 'role', 'is_active']);
        });
    }
};
