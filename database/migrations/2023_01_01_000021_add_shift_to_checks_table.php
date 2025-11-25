<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftToChecksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->enum('shift', ['Shift 1', 'Shift 2'])
                  ->nullable()
                  ->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
}
