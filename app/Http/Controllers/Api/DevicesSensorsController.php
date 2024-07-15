<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Devices;
use App\Helpers\ApiHelpers;
use App\Models\DevicesSensors;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DevicesSensorsController extends Controller
{
    public function add(Request $request)
    {
        try {
            $devices = Devices::find($request->input('device_id'));

            $dateTime = now();

            $year = $dateTime->year;
            $month = $dateTime->month;
            $day = $dateTime->day;
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

            return ApiHelpers::success($data, 'Berhasil mengirim data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function data_by_summary(Request $request)
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

    public function data_by_id(Request $request)
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
