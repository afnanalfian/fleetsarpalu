<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchedulesAgustusSeeder extends Seeder
{
    /**
     * Seeder jadwal Bulan Agustus 2026
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

        // Data jadwal per regu (tanggal 1–31 Agustus 2026)
        $schedules = [
            1 => [ // ALFA
            //  1     2     3     4     5     6     7     8     9    10
                'S2', 'LB', 'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'S1', 'S2', 'R',  'LB', 'LB', 'LB', 'S1', 'S2', 'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',  'R',  'LB', 'S1', 'S2',
            ],
            2 => [ // BRAVO
            //  1     2     3     4     5     6     7     8     9    10
                'LB', 'LB', 'R',  'R',  'S1', 'S2', 'R',  'LB', 'LB', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'S1', 'S2', 'R',  'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'R',
            ],
            3 => [ // CHARLI
            //  1     2     3     4     5     6     7     8     9    10
                'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',  'LB', 'LB', 'S1',
            // 11    12    13    14    15    16    17    18    19    20
                'S2', 'R',  'R',  'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',
            ],
            4 => [ // DELTA
            //  1     2     3     4     5     6     7     8     9    10
                'LB', 'LB', 'S1', 'S2', 'R',  'R',  'R',  'LB', 'S1', 'S2',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',  'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'S1', 'S2', 'LB', 'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',
            ],
            5 => [ // ECHO
            //  1     2     3     4     5     6     7     8     9    10
                'LB', 'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',  'R',  'R',
            // 21    22    23    24    25    26    27    28    29    30    31
                'S1', 'S2', 'LB', 'LB', 'R',  'R',  'S1', 'S2', 'R',  'LB', 'LB',
            ],
            6 => [ // FOXTROT
            //  1     2     3     4     5     6     7     8     9    10
                'S1', 'S2', 'R',  'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'R',  'S1', 'S2', 'LB', 'LB', 'LB', 'R',  'S1', 'S2',
            // 21    22    23    24    25    26    27    28    29    30    31
                'R',  'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',  'LB', 'LB', 'S1',
            ],
        ];

        $rows = [];

        foreach ($schedules as $teamId => $shifts) {
            foreach ($shifts as $index => $shift) {
                $day  = $index + 1;
                $date = sprintf('2026-08-%02d', $day);

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
