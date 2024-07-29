<?php

namespace App\Http\Controllers\Api;

use Exception;

use App\Models\Devices;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Models\DevicesDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class DevicesController extends Controller
{

    public function list(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 403);
            }

            $devices = Devices::all();

            $data = $devices->map(function ($device) {
                $dashboard = DevicesDashboard::where('devices_id', $device->id)->first();
                return [
                    'id' => $device['id'],
                    'name' => $device['name'],
                    'automatic' => $device['automatic'],
                    'heater' => $device['heater'],
                    'blower' => $device['blower'],
                    'dashboard' => [
                        'temperature' => $dashboard['temperature'],
                        'humidity' => $dashboard['humidity'],
                        'ammonia' => $dashboard['ammonia'],
                        'time' => $dashboard['time']
                    ]
                ];
            });

            return ApiHelpers::ok($data, 'Berhasil mengambil seluruh data Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function register(Request $request)
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 403);
            }

            $validator = [
                'name' => $request->input('name'),
                'automatic' => true,
                'heater' => false,
                'blower' => false
            ];

            if(!$validator['name'])
            {
                return ApiHelpers::badRequest([], 'Nama Device tidak boleh kosong!', 400);
            }

            $existingDevice = Devices::where('name', $validator['name'])->first();
            if ($existingDevice) {
                return ApiHelpers::badRequest([], 'Device dengan nama tersebut sudah terdaftar!', 400);
            }

            Devices::create($validator);
            event(new Registered($validator));

            $device = Devices::where('name', $validator['name'])->first();
            if (!$device) {
                return ApiHelpers::badRequest([], 'Device tidak ditemukan setelah pendaftaran!', 500);
            }

            DevicesDashboard::create([
                'devices_id' => $device->id,
                'temperature' => '0',
                'humidity' => '0',
                'ammonia' => '0',
                'time' => ''
            ]);


            $token = $device->createToken($request->name, ['devices'])->plainTextToken;

            $data = [
                'token' => "Bearer $token",
                'device' => $device
            ];

            return ApiHelpers::ok($data, 'Berhasil Mendaftarkan Device Baru!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function renew(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai! ', 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!');
            }

            $validated = $validator->validated();

            $devices = Devices::where('name', $validated['name'])->first();
            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Data Tidak Ditemukan atau Password Salah!', 401);
            }

            $devices->tokens()->delete();
            $token = $devices->createToken($devices->name, ['devices'])->plainTextToken;

            $data = [
                'token' => "Bearer $token",
                'device' => $devices,
            ];

            return ApiHelpers::ok($data, 'Token di update!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }


    public function details(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $devices_id = $request->header('device_id');

            $devices = Devices::findOrFail($devices_id);
            $dashboard = DevicesDashboard::where('devices_id', $devices_id)->first();

            $data = [
                'devices' => $devices,
                'dashboard' => $dashboard
            ];

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function current_users(Request $request)
    {
        try{
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!');
            }

            $devices = Devices::findOrFail($request->header('device_id'));

            if (!$devices)
            {
                return ApiHelpers::badRequest([], 'Devices tidak ditemukan!', 404);
            }

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function current_devices(Request $request)
    {
        try {
            $devices = Auth::check();

            if(!$devices)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $devices = Devices::findOrFail(Auth::user()->id);

            if (!$devices)
            {
                return ApiHelpers::badRequest([], 'Devices tidak ditemukan!', 404);
            }

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::ok($data, 'Ini adalah Detail Devices!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }

    public function changes(Request $request)
    {
        try {
            $users = Auth::check();

            if(!$users)
            {
                return ApiHelpers::badRequest([], 'Token tidak ditemukan, atau tidak sesuai!', 401);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'automatic' => 'required|in:0,1,true,false',
                'heater' => 'required|in:0,1,true,false',
                'blower' => 'required|in:0,1,true,false'
            ]);

            if ($validator->fails()) {
                return ApiHelpers::badRequest($validator->errors(), 'Ada data yang tidak valid!');
            }

            $validated = $validator->validated();

            $validated['automatic'] = filter_var($validated['automatic'], FILTER_VALIDATE_BOOLEAN);
            $validated['heater'] = filter_var($validated['heater'], FILTER_VALIDATE_BOOLEAN);
            $validated['blower'] = filter_var($validated['blower'], FILTER_VALIDATE_BOOLEAN);

            $devices = Devices::where('id', $validated['id'])->first();

            if (!$devices) {
                return ApiHelpers::badRequest([], 'Device tidak ditemukan atau tidak memiliki izin!', 404);
            }

            $devices->update([
                'automatic' => $validated['automatic'],
                'heater' => $validated['heater'],
                'blower' => $validated['blower']
            ]);

            $data = [
                'id' => $devices->id,
                'automatic' => $devices->automatic,
                'heater' => $devices->heater,
                'blower' => $devices->blower,
            ];

            return ApiHelpers::success($data, 'Berhasil Memperbarui Data Sensor!');
        } catch (Exception $e) {
            Log::error($e);
            return ApiHelpers::internalServer($e, 'Terjadi Kesalahan');
        }
    }
}
