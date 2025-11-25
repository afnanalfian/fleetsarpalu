<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FlatsarSeeder extends Seeder
{
    private function makeEmail($name)
    {
        $base = strtolower(
            preg_replace('/[^a-z]/i', '', explode(' ', $name)[0])
        );

        $email = $base.'@fleetsar.test';
        $i = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base.$i.'@fleetsar.test';
            $i++;
        }

        return $email;
    }

    public function run(): void
    {
        /* ===========================
         *  Insert Teams
         * =========================== */
        $teams = ['ALFA', 'BRAVO', 'CHARLIE', 'DELTA', 'ECHO'];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team,
                'leader_id' => null,
            ]);
        }

        $teamIds = Team::pluck('id','name');


        /* ===========================
         *  Insert Master Users
         * =========================== */
        $masters = [
            'ADMIN' => 'Admin',
            'AFRIZAL SOELAIMAN, S.E.' => 'Kepala Sumber Daya',
        ];

        foreach ($masters as $name => $role) {
            User::create([
                'name' => $name,
                'role' => $role,
                'team_id' => null,
                'email' => $this->makeEmail($name),
                'password' => Hash::make('password'),
            ]);
        }


        /* ===========================
         *  Insert Team Users
         * =========================== */
        $teamMembers = [

            'ALFA' => [
                'NAHDA, S.E.' => 'Pegawai',
                'ANDI MAHARDIKA L.' => 'Pegawai',
                'MARIO PASKHALIS R.A' => 'Ketua Tim',
                'MUHTAR' => 'Pegawai',
                'ALI FAJAR Z.' => 'Pegawai',
            ],

            'BRAVO' => [
                'FERAWATI DANI, A.Md.' => 'Pegawai',
                'OGI KURNIAWAN' => 'Pegawai',
                'DIRMAN SANDEWA, S.AP.' => 'Ketua Tim',
                'BAHTIAR' => 'Pegawai',
                'MOH. RIVAI' => 'Pegawai',
            ],

            'CHARLIE' => [
                'AHMAD CANDIANGE, S.E.' => 'Pegawai',
                'A. SAFRULLAH' => 'Pegawai',
                'RICKY MALLAWAN' => 'Ketua Tim',
                'TAKDIR. Z' => 'Pegawai',
                'JABBAR' => 'Pegawai',
            ],

            'DELTA' => [
                'MANDASARI H.' => 'Pegawai',
                'YULIA SARI P.B.' => 'Pegawai',
                'DIMAS TRIATMOJO' => 'Ketua Tim',
                'RIAN R KATILI' => 'Pegawai',
                'TAHRIZAL A. R.' => 'Pegawai',
                'IRVAN RAHARJAN' => 'Pegawai',
            ],

            'ECHO' => [
                'HARYANTO' => 'Pegawai',
                'IMAM TAUFIQ' => 'Pegawai',
                'SAYUDI YUSUF G' => 'Ketua Tim',
                'ADIANSYAH' => 'Pegawai',
                'MOH. AGUS BUDIMAN' => 'Pegawai',
            ],

        ];

        foreach ($teamMembers as $teamName => $members) {
            foreach ($members as $name => $role) {
                User::create([
                    'name' => $name,
                    'role' => $role,
                    'team_id' => $teamIds[$teamName],
                    'email' => $this->makeEmail($name),
                    'password' => Hash::make('password')
                ]);
            }
        }

        // update leader id
        foreach ($teamMembers as $teamName => $members) {

            $leaderName = collect($members)
                ->filter(fn($r) => $r == 'Ketua Tim')
                ->keys()
                ->first();

            if ($leaderName) {
                $leader = User::where('name',$leaderName)->first();

                Team::where('name',$teamName)
                    ->update([
                        'leader_id' => $leader->id
                    ]);
            }
        }


        /* ===========================
         *  Insert Vehicles
         * =========================== */

        $vehicles = [
            ["Rescue Car Double cabin 01", "DN 8870 A", 3020101003],
            ["Rescue Car Double cabin 02", "B 9425 POR", 3020101004],
            ["Rescue Car Double cabin Hilux", "B 9228 PSE", 3020105129],
            ["Rescue Car Carrie Commob", "B 1577 PQR", 3020105060],
            ["Rescue Car Carrie Ambulance", "B 1072 PQR", 3020105061],
            ["Truck Personil 03", "B 9599 PQR", 3020105062],
            ["Truck Personil 06", "B 9986 POQ", 3020105063],
            ["Rescue Truck", "B 9091 PQR", 3020105064],
            ["Truck Pengangkut ATV", "B 9033 POR", 3020101005],
        ];

        foreach ($vehicles as $v) {
            Vehicle::create([
                'name' => $v[0],
                'plat_nomor' => $v[1],
                'kode_bmn' => $v[2],
                'status' => 'available',
            ]);
        }
    }
}
