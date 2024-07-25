<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $date = '2024-06-20';
        $device_id = 1; // Replace with your actual device ID

        // Sample data for each 5-minute interval (288 intervals in 24 hours)
        $temperatures = [
            20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43,
            // Continue with more values as needed
        ];
        $humidities = [
            60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71,
            72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83,
            // Continue with more values as needed
        ];
        $ammonias = [
            5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 10,
            11, 12, 13, 14, 15, 5, 6, 7, 8, 9, 10, 11,
            // Continue with more values as needed
        ];
            
        for ($i = 0; $i < 288; $i++) {
            $timestamp = Carbon::create($date)->addMinutes($i * 5);

            DB::table('devices_sensors')->insert([
                'devices_id' => $device_id,
                'year' => $timestamp->year,
                'month' => $timestamp->month,
                'day' => $timestamp->day,
                'timestamp' => $timestamp->format('H:i:s'),
                'temperature' => $temperatures[$i % count($temperatures)],
                'humidity' => $humidities[$i % count($humidities)],
                'ammonia' => $ammonias[$i % count($ammonias)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // public function run()
    // {
    //     $device_id = 1; // Replace with your actual device ID

    //     // Sample data for each 5-minute interval (288 intervals in 24 hours)
    //     $temperatures = [
    //         20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
    //         32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43,
    //         // Continue with more values as needed
    //     ];
    //     $humidities = [
    //         60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71,
    //         72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83,
    //     ];
    //     $ammonias = [
    //         5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 10,
    //         11, 12, 13, 14, 15, 5, 6, 7, 8, 9, 10, 11,
    //     ];

    //     $startDate = Carbon::create(2024, 6, 1); // Start of June 2024
    //     $endDate = Carbon::create(2024, 6, 30)->endOfDay(); // End of June 2024

    //     while ($startDate <= $endDate) {
    //         for ($i = 0; $i < 288; $i++) {
    //             $timestamp = $startDate->addDay()->addMinutes($i * 5);

    //             DB::table('devices_sensors')->insert([
    //                 'devices_id' => $device_id,
    //                 'year' => $timestamp->year,
    //                 'month' => $timestamp->month,
    //                 'day' => $timestamp->day,
    //                 'timestamp' => $timestamp->format('H:i:s'),
    //                 'temperature' => $temperatures[$i % count($temperatures)],
    //                 'humidity' => $humidities[$i % count($humidities)],
    //                 'ammonia' => $ammonias[$i % count($ammonias)],
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }

    //         $startDate->addDay(); // Move to the next day
    //     }
    // }
}
