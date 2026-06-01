<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalJuni2026Seeder extends Seeder
{
    /**
     * Seeder jadwal bulan Juni 2026.
     * Sumber: Tabel jadwal regu ALFA, BRAVO, CHARLIE, DELTA, ECHO
     *
     * team_id: 1=ALFA, 2=BRAVO, 3=CHARLIE, 4=DELTA, 5=ECHO
     * shift  : S1, S2, R, LB
     */
    public function run(): void
    {
        // Format: team_id => [tgl1, tgl2, ..., tgl30]
        $schedules = [
            1 => [ // ALFA
                'S1','S2','R','R','R','S1','S2','R','R','R',
                'S1','S2','LB','LB','R','S1','S2','R','R','LB',
                'S1','S2','R','R','R','S1','S2','LB','R','R',
            ],
            2 => [ // BRAVO
                'S2','R','R','R','S1','S2','LB','R','R','S1',
                'S2','R','LB','LB','S1','S2','R','R','R','S1',
                'S2','R','R','R','R','S1','S2','LB','LB','R',
            ],
            3 => [ // CHARLIE
                'LB','R','R','S1','S2','LB','LB','R','S1','S2',
                'R','R','LB','S1','S2','R','R','R','S1','S2',
                'LB','R','R','S1','S2','R','LB','LB','S1','S2',
            ],
            4 => [ // DELTA
                'LB','R','S1','S2','R','LB','LB','S1','S2','R',
                'R','R','S1','S2','R','R','R','R','S1','LB',
                'LB','R','S1','S2','R','R','LB','S1','S2','R',
            ],
            5 => [ // ECHO
                'LB','S1','S2','R','R','LB','S1','S2','R','R',
                'R','S1','S2','LB','R','R','S1','S2','R','LB',
                'LB','S1','S2','R','R','R','S1','S2','R','R',
            ],
        ];

        $now = Carbon::now();
        $data = [];

        foreach ($schedules as $teamId => $shifts) {
            foreach ($shifts as $index => $shift) {
                $day   = $index + 1; // 1–30
                $date  = sprintf('2026-06-%02d', $day);

                $data[] = [
                    'team_id'    => $teamId,
                    'date'       => $date,
                    'shift'      => $shift,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert semua sekaligus (chunk 100 untuk keamanan)
        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('schedules')->insert($chunk);
        }

        $this->command->info('Seeder SchedulesJune2026Seeder berhasil: ' . count($data) . ' record dimasukkan.');
    }
}
