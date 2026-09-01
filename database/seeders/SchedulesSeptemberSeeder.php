<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchedulesSeptemberSeeder extends Seeder
{
    /**
     * Seeder jadwal Bulan September 2026
     * Hanya 5 regu (FOXTROT dihapus)
     *
     * Team IDs:
     *  1 = ALFA
     *  2 = BRAVO
     *  3 = CHARLIE
     *  4 = DELTA
     *  5 = ECHO
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Data jadwal per regu (tanggal 1–30 September 2026)
        $schedules = [
            1 => [ // ALFA
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',  'S1', 'S2',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',  'R',  'S1', 'S2',
            // 21    22    23    24    25    26    27    28    29    30
                'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',  'S1', 'S2',
            ],
            2 => [ // BRAVO
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'R',  'S1', 'S2', 'LB', 'LB', 'R',  'S1', 'S2', 'R',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',  'S1', 'S2', 'LB',
            // 21    22    23    24    25    26    27    28    29    30
                'R',  'R',  'S1', 'S2', 'R',  'LB', 'LB', 'S1', 'S2', 'R',
            ],
            3 => [ // CHARLI
            //  1     2     3     4     5     6     7     8     9    10
                'R',  'S1', 'S2', 'R',  'LB', 'LB', 'S1', 'S2', 'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'R',  'S1', 'S2', 'R',  'R',  'R',  'S1', 'S2', 'LB', 'LB',
            // 21    22    23    24    25    26    27    28    29    30
                'R',  'S1', 'S2', 'R',  'R',  'LB', 'S1', 'S2', 'R',  'R',
            ],
            4 => [ // DELTA
            //  1     2     3     4     5     6     7     8     9    10
                'S1', 'S2', 'R',  'R',  'LB', 'S1', 'S2', 'R',  'R',  'R',
            // 11    12    13    14    15    16    17    18    19    20
                'S1', 'S2', 'LB', 'R',  'R',  'S1', 'S2', 'R',  'LB', 'LB',
            // 21    22    23    24    25    26    27    28    29    30
                'S1', 'S2', 'R',  'R',  'R',  'S1', 'S2', 'R',  'R',  'R',
            ],
            5 => [ // ECHO
            //  1     2     3     4     5     6     7     8     9    10
                'S2', 'R',  'R',  'R',  'S1', 'S2', 'R',  'R',  'R',  'S1',
            // 11    12    13    14    15    16    17    18    19    20
                'S2', 'LB', 'LB', 'R',  'S1', 'S2', 'R',  'R',  'LB', 'S1',
            // 21    22    23    24    25    26    27    28    29    30
                'S2', 'R',  'R',  'R',  'S1', 'S2', 'LB', 'R',  'R',  'S1',
            ],
        ];

        $rows = [];

        foreach ($schedules as $teamId => $shifts) {
            foreach ($shifts as $index => $shift) {
                $day  = $index + 1;
                $date = sprintf('2026-09-%02d', $day);

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
