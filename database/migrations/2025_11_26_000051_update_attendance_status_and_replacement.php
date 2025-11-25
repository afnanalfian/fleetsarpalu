<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {

            // Hapus kolom lama
            $table->dropColumn('present');
            $table->dropColumn('reason');

            // Tambah kolom status baru
            $table->enum('status', [
                'Hadir', 'Sakit', 'Izin', 'Cuti', 'Tanpa Keterangan'
            ])->default('Hadir');

            // Kolom pengganti
            $table->foreignId('replacement_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('is_replacement')->default(false);

            // Catatan opsional
            $table->string('notes')->nullable();
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
