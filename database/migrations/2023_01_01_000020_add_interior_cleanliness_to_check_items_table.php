<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInteriorCleanlinessToCheckItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('check_items', function (Blueprint $table) {
            $table->boolean('interior_cleanliness_ok')->nullable()->after('body_cleanliness_note');
            $table->string('interior_cleanliness_note')->nullable()->after('interior_cleanliness_ok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('check_items', function (Blueprint $table) {
            $table->dropColumn([
                'interior_cleanliness_ok',
                'interior_cleanliness_note',
            ]);
        });
    }
}
