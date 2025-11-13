<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Team;
use App\Models\Vehicle;

class FlatsarSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // 1. ADMIN & SUMDA
        // ============================
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
            'role' => 'Sumda',
        ]);

        // ============================
        // 2. DATA TIM
        // ============================
        $teamNames = [
            'Tim Alpha',
            'Tim Bravo',
            'Tim Charlie',
            'Tim Delta',
            'Tim Echo',
        ];

        $nipCounter = 1;

        foreach ($teamNames as $name) {
            $team = Team::create(['name' => $name]);

            // Generate NIP unik
            $nip = '20250000' . str_pad($nipCounter++, 4, '0', STR_PAD_LEFT);

            // Ketua Tim
            $leader = User::create([
                'name'      => "Ketua $name",
                'email'     => strtolower(str_replace(' ', '', $name)) . "_leader@flatsar.test",
                'password'  => Hash::make('password'),
                'role'      => 'Ketua Tim',
                'team_id'   => $team->id,
                'NIP'       => $nip,
                'phone'     => '08' . rand(1000000000, 9999999999),
            ]);

            $team->update(['leader_id' => $leader->id]);

            // 2 Anggota
            for ($i = 1; $i <= 2; $i++) {

                // Generate NIP unik
                $nip = '20250000' . str_pad($nipCounter++, 4, '0', STR_PAD_LEFT);

                User::create([
                    'name'      => "Anggota $i $name",
                    'email'     => strtolower(str_replace(' ', '', $name)) . "_anggota$i@flatsar.test",
                    'password'  => Hash::make('password'),
                    'role'      => 'Pegawai',
                    'team_id'   => $team->id,
                    'NIP'       => $nip,
                    'phone'     => '08' . rand(1000000000, 9999999999),
                ]);
            }
        }


        // ============================
        // 3. DATA KENDARAAN
        // ============================
        $vehicles = [
            [
                'kode_bmn'   => '3020101003',
                'name'       => 'Rescue Car Double Cabin 01',
                'merk'       => 'Toyota',
                'plat'       => 'DN 8870 A',
                'tipe'       => 'Double Cabin',
            ],
            [
                'kode_bmn'   => '3020101004',
                'name'       => 'Rescue Car Double Cabin 02',
                'merk'       => 'Toyota',
                'plat'       => 'B 9425 POR',
                'tipe'       => 'Double Cabin',
            ],
            [
                'kode_bmn'   => '3020105129',
                'name'       => 'Rescue Car Double Cabin Hilux',
                'merk'       => 'Toyota Hilux',
                'plat'       => 'B 9228 PSE',
                'tipe'       => 'Double Cabin',
            ],
            [
                'kode_bmn'   => '3020105060',
                'name'       => 'Rescue Car Carry Commob',
                'merk'       => 'Suzuki',
                'plat'       => 'B 1577 PQR',
                'tipe'       => 'Carry',
            ],
            [
                'kode_bmn'   => '3020105061',
                'name'       => 'Rescue Car Carry Ambulance',
                'merk'       => 'Suzuki',
                'plat'       => 'B 1072 PQR',
                'tipe'       => 'Carry Ambulance',
            ],
            [
                'kode_bmn'   => '3020105062',
                'name'       => 'Truck Personil 03',
                'merk'       => 'Isuzu',
                'plat'       => 'B 9599 PQR',
                'tipe'       => 'Truck Personil',
            ],
            [
                'kode_bmn'   => '3020105063',
                'name'       => 'Truck Personil 06',
                'merk'       => 'Isuzu',
                'plat'       => 'B 9986 POQ',
                'tipe'       => 'Truck Personil',
            ],
            [
                'kode_bmn'   => '3020105064',
                'name'       => 'Rescue Truck',
                'merk'       => 'Mitsubishi',
                'plat'       => 'B 9091 PQR',
                'tipe'       => 'Rescue Truck',
            ],
            [
                'kode_bmn'   => '3020105065',
                'name'       => 'Truck Pengangkut ATV',
                'merk'       => 'Toyota',
                'plat'       => 'B 9033 POR',
                'tipe'       => 'Truck Pengangkut',
            ],
        ];

        $dummyCounter = 1;

        foreach ($vehicles as $v) {

            // Auto generate BMN jika null
            $kodeBMN = $v['kode_bmn'] ?? 'DUMMY-' . str_pad($dummyCounter++, 5, '0', STR_PAD_LEFT);

            Vehicle::create([
                'kode_bmn'          => $kodeBMN,
                'name'              => $v['name'],
                'merk'              => $v['merk'],
                'plat_nomor'        => $v['plat'],
                'tipe'              => $v['tipe'],
                'year'              => 2020,
                'factory'           => $v['merk'],
                'load_capacity'     => 0,
                'weight'            => 0,
                'bahan_bakar'       => 'Solar',
                'lokasi'            => 'Markas Utama',
                'warna'             => 'Putih',
                'status'            => 'available',
                'fuel_percent'      => rand(40, 100),
                'distance'          => rand(10000, 50000),
                'last_km_for_oil'   => rand(5000, 9000),
                'notes'             => 'Data awal kendaraan.',
                'photo_path'        => null,
            ]);
        }
    }
}
