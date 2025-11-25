<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->onDelete('cascade');

            $table->date('date');

            $table->enum('shift', ['S1', 'S2', 'R', 'LB']);

            $table->timestamps();

            // Kombinasi tanggal + team unik (1 team 1 shift per hari)
            $table->unique(['team_id', 'date']);

            // Mempercepat query dashboard
            $table->index('date');
            $table->index('shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
