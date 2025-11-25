<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('NIP')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('role', ['Admin', 'Kepala Sumber Daya', 'Ketua Tim', 'Pegawai']);

            // TANPA FOREIGN KEY DULU
            $table->unsignedBigInteger('team_id')->nullable();

            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
