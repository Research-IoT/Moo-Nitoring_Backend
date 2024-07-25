<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Helpers\ApiHelpers;

use App\Models\DevicesDashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DevicesDashboardController extends Controller
{
    public function update(Request $request)
    {
        try {
            $devices = Auth::check();
            $devicesId = $request->user()->id;

            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $dateTime = now()->setTimezone('Asia/Jakarta');

            $time = $dateTime->toTimeString();

            $validator = Validator::make($request->all(), [
                'temperature' => 'required|string|max:125',
                'humidity' => 'required|string|max:125',
                'ammonia' => 'required|string|max:125'
            ]);

            if ($validator->fails()) {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!', 403);
            }

            $validated = $validator->validated();

            $dashboard = DevicesDashboard::where('devices_id', $devicesId)->first();
            
            $dashboard->update([
                'device_id' => $devicesId,
                'temperature' => $validated['temperature'],
                'humidity' => $validated['humidity'],
                'ammonia' => $validated['ammonia'],
                'time' => $time
            ]);

            $data = $dashboard;

            return ApiHelpers::success($data, 'Berhasil mengirim data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function info(Request $request)
    {
        try {
            $dashboard = DevicesDashboard::where('devices_id', $request->header('device_id'))->first();

            return ApiHelpers::ok($dashboard, 'Berhasil mengambil terkini data!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
