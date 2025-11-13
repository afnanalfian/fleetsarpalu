<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('use_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_request_id')->constrained('borrow_requests')->onDelete('cascade');

            $table->integer('fuel_before')->nullable();
            $table->integer('fuel_after')->nullable();
            $table->integer('km_before')->nullable();
            $table->integer('km_after')->nullable();

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

            $table->string('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('use_reports');
    }
};
