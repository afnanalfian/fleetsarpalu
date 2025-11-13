<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusEnumInBorrowRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->enum('status', [
                'Pending', 'Approved', 'Rejected', 'In Use', 'Completed', 'Cancelled'
            ])->default('Pending')->after('surat_tugas_path');
        });
    }

    public function down(): void
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->enum('status', [
                'Pending', 'Approved', 'Rejected', 'In Use', 'Completed'
            ])->default('Pending')->after('surat_tugas_path');
        });
    }
}
