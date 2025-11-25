<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->constrained('checks')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->integer('fuel_percent')->nullable();
            $table->integer('km')->nullable();

            $table->boolean('radiator_ok')->nullable();
            $table->string('radiator_note')->nullable();

            $table->boolean('air_filter_ok')->nullable();
            $table->string('air_filter_note')->nullable();

            $table->boolean('wiper_ok')->nullable();
            $table->string('wiper_note')->nullable();

            $table->boolean('lights_ok')->nullable();
            $table->string('lights_note')->nullable();

            $table->boolean('leaks_ok')->nullable();
            $table->string('leaks_note')->nullable();

            $table->boolean('hazards_ok')->nullable();
            $table->string('hazards_note')->nullable();

            $table->boolean('horn_ok')->nullable();
            $table->string('horn_note')->nullable();

            $table->boolean('siren_ok')->nullable();
            $table->string('siren_note')->nullable();

            $table->boolean('tires_ok')->nullable();
            $table->string('tires_note')->nullable();

            $table->boolean('brakes_ok')->nullable();
            $table->string('brakes_note')->nullable();

            $table->boolean('battery_ok')->nullable();
            $table->string('battery_note')->nullable();

            $table->boolean('start_engine_ok')->nullable();
            $table->string('start_engine_note')->nullable();

            $table->boolean('glass_cleanliness_ok')->nullable();
            $table->string('glass_cleanliness_note')->nullable();

            $table->boolean('body_cleanliness_ok')->nullable();
            $table->string('body_cleanliness_note')->nullable();

            $table->string('photos')->nullable();
            $table->enum('condition', ['Baik', 'Rusak'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('check_items');
    }
};
