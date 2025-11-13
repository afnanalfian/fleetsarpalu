<?php

namespace Database\Seeders;

use App\Models\kendaraan;
use App\Models\pegawai;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
       $this->call(FlatsarSeeder::class);
    }
}
