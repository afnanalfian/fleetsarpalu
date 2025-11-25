<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        /**
         * ============================
         * CHECK ITEMS
         * ============================
         */
        Schema::table('check_items', function (Blueprint $table) {

            // Mekanis: 3 kondisi
            $mechanical = [
                'radiator_ok','air_filter_ok','wiper_ok','lights_ok','leaks_ok',
                'hazards_ok','horn_ok','siren_ok','tires_ok','brakes_ok','battery_ok','start_engine_ok'
            ];

            foreach ($mechanical as $f) {
                $table->enum($f, ['Baik', 'Rusak Ringan', 'Rusak Berat'])
                      ->nullable()
                      ->change();
            }

            // Kebersihan: 2 kondisi
            $cleanliness = [
                'glass_cleanliness_ok','body_cleanliness_ok','interior_cleanliness_ok'
            ];

            foreach ($cleanliness as $f) {
                $table->enum($f, ['Bersih', 'Tidak Bersih'])
                      ->nullable()
                      ->change();
            }

            // Overall condition enum
            $table->enum('condition', ['Baik', 'Rusak Ringan', 'Rusak Berat'])
                  ->default('Baik')
                  ->change();
        });

        /**
         * ============================
         * USE REPORTS
         * ============================
         * (hanya mekanis)
         */
        Schema::table('use_reports', function (Blueprint $table) {

            $fields = [
                'hazards_ok','horn_ok','siren_ok','tires_ok',
                'brakes_ok','battery_ok','start_engine_ok'
            ];

            foreach ($fields as $f) {
                $table->enum($f, ['Baik', 'Rusak Ringan', 'Rusak Berat'])
                      ->nullable()
                      ->change();
            }
        });
    }

    public function down()
    {
        /**
         * Kembalikan ke boolean/int dan enum lama
         * (Jika diperlukan rollback)
         */
        Schema::table('check_items', function (Blueprint $table) {

            // Mekanis kembali ke boolean
            $mechanical = [
                'radiator_ok','air_filter_ok','wiper_ok','lights_ok','leaks_ok',
                'hazards_ok','horn_ok','siren_ok','tires_ok','brakes_ok','battery_ok','start_engine_ok'
            ];

            foreach ($mechanical as $f) {
                $table->boolean($f)->nullable()->change();
            }

            // Kebersihan kembali ke boolean
            $cleanliness = [
                'glass_cleanliness_ok','body_cleanliness_ok','interior_cleanliness_ok'
            ];

            foreach ($cleanliness as $f) {
                $table->boolean($f)->nullable()->change();
            }

            // Condition kembali
            $table->enum('condition', ['Baik', 'Rusak'])
                  ->default('Baik')
                  ->change();
        });

        Schema::table('use_reports', function (Blueprint $table) {
            $fields = [
                'hazards_ok','horn_ok','siren_ok','tires_ok',
                'brakes_ok','battery_ok','start_engine_ok'
            ];
            foreach ($fields as $f) {
                $table->boolean($f)->nullable()->change();
            }
        });
    }
};
