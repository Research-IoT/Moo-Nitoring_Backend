<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Devices;
use App\Helpers\ApiHelpers;
use App\Helpers\FirebaseFCM;
use App\Models\DevicesSensors;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
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

    public function bySummary(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $data = DevicesSensors::all();

            return ApiHelpers::ok($data, 'Berhasil mengambil seluruh data!');
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

            $data = DevicesSensors::where('day', $request->header('day'))->get();

            return ApiHelpers::ok($data, 'Berhasil mengambil seluruh data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function byId(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $devices = Devices::find($request->header('device_id'));

            $data = $devices->sensor()->get();

            return ApiHelpers::ok($data, 'Berhasil mengambil seluruh data!');
        } catch (Exception $e) {
            return ApiHelpers::badRequest($e, 'Terjadi Kesalahan');
        }
    }

    public function current(Request $request)
    {
        try {
            $devices = Devices::find($request->header('device_id'));

            $data = $devices->sensor()->orderBy('created_at', 'desc')->first();

            return ApiHelpers::ok($data, 'Berhasil mengambil terkini data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
