<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bmn')->unique();
            $table->string('name');
            $table->integer('distance')->default(0);
            $table->year('year')->nullable();
            $table->string('merk')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->string('tipe')->nullable();
            $table->string('factory')->nullable();
            $table->integer('load_capacity')->nullable();
            $table->integer('weight')->nullable();
            $table->string('bahan_bakar')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('warna')->nullable();
            $table->enum('status', ['available', 'is_use', 'unavailable'])->default('available');
            $table->integer('last_km_for_oil')->nullable();
            $table->integer('fuel_percent')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehicles');
    }
};
