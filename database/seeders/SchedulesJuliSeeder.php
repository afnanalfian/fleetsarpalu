<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchedulesJuliSeeder extends Seeder
{
    /**
     * Seeder jadwal Bulan Juli 2026
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

        // Data jadwal per regu (tanggal 1–31 Juli 2026)
        // Index array = tanggal (1-based)
        $schedules = [
            1 => [ // ALFA
            //  1     2     3     4     5     6     7     8     9    10
                'S1', 'S2', 'R',  'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'LB', 'LB', 'S2', 'R',  'R',  'R',  'R',  'LB', 'LB', 'S1',
            // 21    22    23    24    25    26    27    28    29    30    31
                'S2', 'R',  'R',  'R',  'S1', 'S2', 'R',  'R',  'R',  'R',  'S1',
            ],
            2 => [ // BRAVO
            //  1     2     3     4     5     6     7     8     9    10
                'S2', 'R',  'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'LB', 'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',  'R',  'S1', 'S2',
            ],
            3 => [ // CHARLIE
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'R',  'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',  'R',  'S1', 'S2', 'R',
            ],
            4 => [ // DELTA
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'R',  'R',  'S1', 'S2', 'R',  'R',  'R',  'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'S1', 'S2', 'LB', 'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'S1', 'S2', 'R',  'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',
            ],
            5 => [ // ECHO
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',  'R',  'S1', 'S2',
            // 11    12    13    14    15    16    17    18    19    20
                'LB', 'LB', 'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'R',  'R',  'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',  'R',
            ],
            6 => [ // FOXTROT
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'S1', 'S2', 'LB', 'LB', 'R',  'R',  'S1', 'S2', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',  'LB', 'LB', 'S1',
            // 21    22    23    24    25    26    27    28    29    30    31
                'S2', 'R',  'R',  'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',  'R',
            ],
        ];

        $rows = [];

        foreach ($schedules as $teamId => $shifts) {
            foreach ($shifts as $index => $shift) {
                $day  = $index + 1; // tanggal 1–31
                $date = sprintf('2026-07-%02d', $day);

                $rows[] = [
                    'team_id'    => $teamId,
                    'date'       => $date,
                    'shift'      => $shift,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('schedules')->insert($rows);
    }
}
