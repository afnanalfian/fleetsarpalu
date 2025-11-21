<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            // Ubah tipe start_at dan end_at menjadi DATE
            $table->date('start_at')->change();
            $table->date('end_at')->change();
        });
    }

    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            // Kembalikan ke datetime jika rollback
            $table->datetime('start_at')->change();
            $table->datetime('end_at')->change();
        });
    }
};
