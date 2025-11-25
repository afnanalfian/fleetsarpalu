<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUseReportsTableAddPhotoPathsAndRemovePhotos extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('use_reports', function (Blueprint $table) {
            $table->dropColumn('photos');
            $table->string('indicator_before_photos_path')->nullable()->after('start_engine_note');
            $table->string('indicator_after_photos_path')->nullable()->after('indicator_before_photos_path');
            $table->string('location_photos_path')->nullable()->after('indicator_after_photos_path');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('use_reports', function (Blueprint $table) {
            $table->string('photos')->nullable();
            $table->dropColumn([
                'indicator_before_photos_path',
                'indicator_after_photos_path',
                'location_photos_path'
            ]);
        });
    }
}
