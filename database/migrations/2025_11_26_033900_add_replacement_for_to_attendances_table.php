<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('replacement_for')->nullable()->after('user_id');

            // Jika ingin membuat FK ke users
            // boleh ditambahkan, tapi optional
            // $table->foreign('replacement_for')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // kalau ada FK, hapus dulu
            // $table->dropForeign(['replacement_for']);

            $table->dropColumn('replacement_for');
        });
    }
};
