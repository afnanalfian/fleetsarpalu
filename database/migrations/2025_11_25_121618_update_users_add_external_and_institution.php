<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom institution
        Schema::table('users', function (Blueprint $table) {
            $table->string('institution')->nullable()->after('phone');
        });

        // 2. Ubah enum role
        // ENUM tidak bisa diubah melalui Schema::table → harus raw SQL
        DB::statement("ALTER TABLE users MODIFY role ENUM(
            'Admin',
            'Kepala Sumber Daya',
            'Ketua Tim',
            'Pegawai',
            'External'
        )");
    }

    public function down(): void
    {
        // Kembalikan enum seperti semula
        DB::statement("ALTER TABLE users MODIFY role ENUM(
            'Admin',
            'Kepala Sumber Daya',
            'Ketua Tim',
            'Pegawai'
        )");

        // Hapus kolom institution
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('institution');
        });
    }
};
