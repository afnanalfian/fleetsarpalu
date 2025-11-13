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
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key lama (jika ada)
            try {
                $table->dropForeign(['team_id']);
            } catch (\Exception $e) {}

            // Ubah kolom agar nullable dan default null
            $table->unsignedBigInteger('team_id')->nullable()->default(null)->change();

            // Tambahkan FK baru yang benar
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
