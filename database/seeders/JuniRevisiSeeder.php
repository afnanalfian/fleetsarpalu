<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JuniRevisiSeeder extends Seeder
{
    /**
     * Seeder jadwal Bulan Juni 2026
     * Berdasarkan tabel jadwal regu: ALFA, BRAVO, CHARLIE, DELTA, ECHO, FOXTROT
     *
     * Team IDs:
     *  1 = ALFA
     *  2 = BRAVO
     *  3 = CHARLIE
     *  4 = DELTA
     *  5 = ECHO
     *  6 = FOXTROT
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Data jadwal per regu (tanggal 1–30 Juni 2026)
        // Index array = tanggal (1-based)
        $schedules = [
            // team_id => [tgl1, tgl2, ..., tgl30]
            1 => [ // ALFA
                'S1','S2','R','R','R','LB','S1','S2','R','R',
                'R','R','S1','S2','R','LB','R','R','S1','S2',
                'LB','R','R','R','S1','S2','LB','LB','R','R',
            ],
            2 => [ // BRAVO
                'S2','R','R','R','R','S1','S2','R','R','R',
                'R','S1','S2','LB','R','LB','R','S1','S2','LB',
                'LB','R','R','S1','S2','R','LB','LB','R','S1',
            ],
            3 => [ // CHARLIE
                'LB','R','R','R','S1','S2','LB','R','R','R',
                'S1','S2','LB','LB','R','LB','S1','S2','R','LB',
                'LB','R','S1','S2','R','R','LB','LB','S1','S2',
            ],
            4 => [ // DELTA
                'LB','R','R','S1','S2','LB','LB','R','R','S1',
                'S2','R','LB','LB','R','S1','S2','R','R','LB',
                'LB','S1','S2','R','R','R','LB','S1','S2','R',
            ],
            5 => [ // ECHO
                'LB','R','S1','S2','R','LB','LB','R','S1','S2',
                'R','R','LB','LB','S1','S2','R','R','R','LB',
                'S1','S2','R','R','R','R','S1','S2','R','R',
            ],
            6 => [ // FOXTROT
                'LB','S1','S2','R','R','LB','LB','S1','S2','R',
                'R','R','LB','S1','S2','LB','R','R','R','S1',
                'S2','R','R','R','R','S1','S2','LB','R','R',
            ],
        ];

        $rows = [];

        foreach ($schedules as $teamId => $shifts) {
            foreach ($shifts as $index => $shift) {
                $day    = $index + 1; // tanggal 1–30
                $date   = sprintf('2026-06-%02d', $day);

                $rows[] = [
                    'team_id'    => $teamId,
                    'date'       => $date,
                    'shift'      => $shift,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert semua sekaligus
        DB::table('schedules')->insert($rows);
    }
}
