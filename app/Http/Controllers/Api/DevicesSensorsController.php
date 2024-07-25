<?php

namespace App\Http\Controllers\Api;

use Exception;

use Carbon\Carbon;

use App\Models\Devices;
use App\Models\Notifications;
use App\Models\DevicesSensors;

use App\Helpers\ApiHelpers;
use App\Helpers\FirebaseFCM;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DevicesSensorsController extends Controller
{
    public function add(Request $request)
    {
        try {
            $devices = Auth::check();

            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $devices = Devices::find($request->user()->id);

            $dateTime = now()->setTimezone('Asia/Jakarta');

            $year = $dateTime->year;
            $month = $dateTime->month;
            $day = $dateTime->day;
            $date = $dateTime->toDateString();
            $time = $dateTime->toTimeString();

            $validated = [
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'timestamp' => $time,
                'temperature' => $request->input('temperature'),
                'humidity' => $request->input('humidity'),
                'ammonia' => $request->input('ammonia'),
            ];

            $data = $devices->sensor()->create($validated);

            if ($request->input('temperature') > 30) {
                FirebaseFCM::withTopic(
                    'Suhu Terlalu Tinggi', 
                    'Menyalakan Blower', 
                    'notifications'
                );

                Notifications::create([
                    'title' => 'Suhu Terlalu Tinggi',
                    'description' => 'Menyalakan Blower',
                    'date' => $date,
                    'time' => $time,
                ]);
            }

            return ApiHelpers::success($data, 'Berhasil mengirim data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function byDay(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $day = $request->header('day');
            $month = $request->header('month');
            $year = $request->header('year');

            $data = DevicesSensors::where('day', $day)
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->get();

            if($data->isEmpty()) 
            {
                return ApiHelpers::badRequest([], 'Data tidak ditemukan!', 404);
            }

            return ApiHelpers::ok($data, 'Berhasil mengambil data harian!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function byWeek(Request $request)
    {
        try {
            $user = Auth::check();

            if (!$user) {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 403);
            }

            $dayStart = $request->header('dayStart');
            $dayEnd = $request->header('dayEnd');
            $month = $request->header('month');
            $year = $request->header('year');

            $data = DevicesSensors::where('day', '>=', $dayStart)
                    ->where('day', '<=', $dayEnd)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->orderBy('day', 'asc')
                    ->get();

            if($data->isEmpty()) 
            {
                return ApiHelpers::badRequest([], 'Data tidak ditemukan!', 404);
            }

            return ApiHelpers::ok($data, 'Berhasil mengambil data mingguan!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }


    public function byMonth(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $month = $request->header('month');
            $year = $request->header('year');

            $data = DevicesSensors::where('month', $month)
                                    ->where('year', $year)
                                    ->get();

            if($data->isEmpty()) 
            {
                return ApiHelpers::badRequest([], 'Data tidak ditemukan!', 404);
            }

            return ApiHelpers::ok($data, 'Berhasil mengambil data bulanan!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
