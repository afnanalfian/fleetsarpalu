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
            $table->enum('role', ['Admin', 'Sumda', 'Ketua Tim', 'Pegawai']);
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
