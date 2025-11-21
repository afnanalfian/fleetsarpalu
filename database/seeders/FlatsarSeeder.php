<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FlatsarSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@flatsar.test',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Petugas Sumda',
            'email' => 'sumda@flatsar.test',
            'password' => Hash::make('password'),
            'role' => 'Kepala Sumber Daya',
        ]);
    }
}
