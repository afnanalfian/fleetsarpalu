<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Tambah FK users.team_id → teams.id
         */
        Schema::table('users', function (Blueprint $table) {
            // pastikan kolom tidak punya FK lama
            $table->dropForeign(['team_id']);

            // pastikan nullable
            $table->unsignedBigInteger('team_id')->nullable()->change();

            // tambah FK
            $table->foreign('team_id')
                  ->references('id')
                  ->on('teams')
                  ->onDelete('set null');
        });

        /**
         * Tambah FK teams.leader_id → users.id
         */
        Schema::table('teams', function (Blueprint $table) {
            // drop FK lama kalau ada
            try {
                $table->dropForeign(['leader_id']);
            } catch (\Exception $e) {}

            // pastikan nullable
            $table->unsignedBigInteger('leader_id')->nullable()->change();

            // tambahkan FK
            $table->foreign('leader_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
        });
    }
};
