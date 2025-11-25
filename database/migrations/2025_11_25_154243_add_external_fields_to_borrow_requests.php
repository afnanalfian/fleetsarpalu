<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            // Lampiran untuk peminjaman external
            $table->string('lampiran_path')->nullable()->after('surat_tugas_path');

            // Supir yang ditunjuk oleh Kepala Sumber Daya
            $table->unsignedBigInteger('driver_id')->nullable()->after('approved_by');

            // Relasi ke tabel users
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
            $table->dropColumn('lampiran_path');
        });
    }
};
