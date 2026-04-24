<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;

class AdditionalFlatsarUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil mapping team yang sudah ada
        $teams = Team::pluck('id', 'name')->toArray();

        $users = [
            ['LA ODE ADITYA I.F, A.MK', '199112182025061001', 'la.firdaus@basarnas.go.id', '082292812021', 'Pegawai', 'ALFA'],
            ['DWI CAHYO S.', '199606302015031001', 'dwi.setyawan@basarnas.go.id', '085395479190', 'Pegawai', 'ALFA'],
            ['MOH. RIANG FAOZI', '199511032015031001', 'moh.faozi@basarnas.go.id', '082296007471', 'Pegawai', 'BRAVO'],
            ['NURHAYATI', '199304112015032001', 'nurhayati@basarnas.go.id', '082293923124', 'Pegawai', 'CHARLIE'],
            ['MUH. SYAIFULLAH ROSYID H, A.Md.', '199407112025061003', 'muhammad.hasyimi@basarnas.go.id', '082234662851', 'Pegawai', 'CHARLIE'],
            ['ASRUL ARIMANSAR, S.AP', '198608052007121001', 'asrul.arimansar@basarnas.go.id', '085145000022', 'Ketua Tim', 'CHARLIE'],
            ['AZWAR A.', '198905252010121003', 'azwar@basarnas.go.id', '085243845213', 'Ketua Tim', 'DELTA'],
            ['MUH. SUGIANTO', '198803122015031003', 'muhammad@basarnas.go.id', '082323667421', 'Pegawai', 'ECHO'],
        ];

        foreach ($users as $u) {

            $teamId = isset($teams[$u[5]]) ? $teams[$u[5]] : null;

            $user = User::create([
                'name' => $u[0],
                'nip' => $u[1],
                'email' => $u[2],
                'phone' => $u[3],
                'role' => $u[4],
                'team_id' => $teamId,
                'institution' => null,
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]);

            // Jika Ketua Tim → bisa kamu sesuaikan kalau mau jadi leader juga
            if ($u[4] === 'Ketua Tim' && $teamId) {
                Team::where('id', $teamId)->update([
                    'leader_id' => $user->id
                ]);
            }
        }
    }
}
