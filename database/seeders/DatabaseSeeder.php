<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $startDate = '2024-08-01';
        $endDate = '2024-08-31';
        $device_id = 1;

        $temperatures = [
            20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43,
        ];
        $humidities = [
            60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71,
            72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83,
        ];
        $ammonias = [
            5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 10,
            11, 12, 13, 14, 15, 5, 6, 7, 8, 9, 10, 11,
        ];

        $baseTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        for ($dayTimestamp = $baseTimestamp; $dayTimestamp <= $endTimestamp; $dayTimestamp += 86400) {
            for ($i = 0; $i < 288; $i++) {
                $timestamp = $dayTimestamp + ($i * 5 * 60);
                $carbonTimestamp = Carbon::createFromTimestamp($timestamp);

                DB::table('devices_sensors')->insert([
                    'devices_id' => $device_id,
                    'year' => $carbonTimestamp->year,
                    'month' => $carbonTimestamp->month,
                    'day' => $carbonTimestamp->day,
                    'timestamp' => $carbonTimestamp->format('H:i:s'),
                    'temperature' => $temperatures[$i % count($temperatures)],
                    'humidity' => $humidities[$i % count($humidities)],
                    'ammonia' => $ammonias[$i % count($ammonias)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
